<?php
/**
 * Analyze Document API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Analyzes uploaded documents to extract tasks/deadlines
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/gemini.php';
require_once __DIR__ . '/../auth/session.php';

/**
 * Extract valid JSON from text that may contain extra content after JSON
 * This handles cases where AI responses include "thought signatures" or other text after the JSON
 * 
 * @param string $text The text containing JSON
 * @return array|null The decoded JSON array or null if no valid JSON found
 */
function extractJsonFromText($text) {
    if (empty($text)) {
        return null;
    }
    
    // Remove thought signature lines if present (Gemini may include thoughtSignature field)
    $text = preg_replace('/\s*"thoughtSignature":\s*"[^"]*"[,\s]*/', '', $text);
    
    // First, try the simple approach - if the entire text is valid JSON
    $json = json_decode($text, true);
    if ($json !== null && json_last_error() === JSON_ERROR_NONE) {
        return $json;
    }
    
    // Find the first opening brace
    $firstBrace = strpos($text, '{');
    if ($firstBrace === false) {
        return null;
    }
    
    // Try extracting JSON starting from each potential opening brace
    for ($i = $firstBrace; $i < strlen($text); $i++) {
        if ($text[$i] === '{') {
            // Try to decode from this position
            $potentialJson = substr($text, $i);
            $decoded = json_decode($potentialJson, true);
            
            if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            
            // If that fails, try finding where the JSON might end and try again
            // Look for common patterns that indicate end of JSON
            $braceCount = 0;
            $jsonEnd = -1;
            for ($j = 0; $j < strlen($potentialJson); $j++) {
                if ($potentialJson[$j] === '{') {
                    $braceCount++;
                } elseif ($potentialJson[$j] === '}') {
                    $braceCount--;
                    if ($braceCount === 0) {
                        $jsonEnd = $j + 1;
                        break;
                    }
                }
            }
            
            if ($jsonEnd > 0) {
                $trimmedJson = substr($potentialJson, 0, $jsonEnd);
                $decoded = json_decode($trimmedJson, true);
                
                if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
        }
    }
    
    // Fallback: try the original greedy regex approach
    if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
        $decoded = json_decode($matches[0], true);
        if ($decoded !== null) {
            return $decoded;
        }
    }
    
    return null;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$filename = $data['filename'] ?? 'document.txt';
$content = $data['content'] ?? '';

if (empty($content)) {
    echo json_encode(['success' => false, 'error' => 'No content to analyze']);
    exit;
}

// Limit content length for API
$content = substr($content, 0, 5000);

// Build prompt for document analysis
$prompt = "Analyze the following document content and extract academic tasks/deadlines. 

Document: $filename

Content:
$content

Provide a JSON response with the following structure:
{
    \"tasks\": [
        {
            \"title\": \"Task title\",
            \"description\": \"Brief description of the task\",
            \"due_date\": \"YYYY-MM-DD format\",
            \"due_time\": \"HH:MM format or null\",
            \"priority\": \"high\", \"medium\", or \"low\",
            \"complexity\": 1-10,
            \"insights\": \"Brief study tips for this task\"
        }
    ]
}

If no clear tasks are found, return an empty tasks array.
Only extract tasks that have clear deadlines or are academic assignments.
Respond ONLY with valid JSON, no other text.";

$result = callGeminiAPI($prompt);

if (!$result || !$result['success']) {
    echo json_encode([
        'success' => false,
        'error' => $result['error'] ?? 'Failed to analyze document'
    ]);
    exit;
}

// Parse JSON from response using the robust extraction function
$text = $result['text'];
$json = extractJsonFromText($text);

if ($json && isset($json['tasks']) && !empty($json['tasks'])) {
    $task = $json['tasks'][0]; // Take first task
    
    echo json_encode([
        'success' => true,
        'task' => [
            'title' => $task['title'] ?? 'Extracted Task',
            'description' => $task['description'] ?? substr($content, 0, 500),
            'due_date' => $task['due_date'] ?? date('Y-m-d', strtotime('+7 days')),
            'due_time' => $task['due_time'] ?? null,
            'priority' => $task['priority'] ?? 'medium',
            'complexity' => $task['complexity'] ?? 5,
            'insights' => $task['insights'] ?? 'Break this task into smaller chunks and study regularly.'
        ],
        'raw_response' => $json
    ]);
} else {
    // Could not parse JSON or no tasks found, create generic task
    echo json_encode([
        'success' => true,
        'task' => [
            'title' => 'Document Review: ' . substr($filename, 0, 30),
            'description' => substr($content, 0, 500),
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'due_time' => null,
            'priority' => 'medium',
            'complexity' => 5,
            'insights' => 'Review the document content and identify key tasks.'
        ]
    ]);
}


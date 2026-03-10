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

// Parse JSON from response
$text = $result['text'];
if (preg_match('/\{.*\}/s', $text, $matches)) {
    $json = json_decode($matches[0], true);
    
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
        // No clear tasks found, create a generic task
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
} else {
    // Could not parse JSON, create generic task
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


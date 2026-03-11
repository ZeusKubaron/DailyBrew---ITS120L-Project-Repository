<?php
/**
 * Analyze Text API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Analyzes text input to extract tasks/deadlines
 * Uses fallback analysis when AI is unavailable
 */

// Disable all error output to browser, but log to a file
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/gemini-debug.log');

// Clear any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

$prompt = $data['prompt'] ?? null;

// Handle document analysis via AI prompt
if ($prompt) {
    // Try AI analysis first
    $analysis = tryAIAnalysis($prompt);
    
    if ($analysis['success']) {
        echo json_encode([
            'success' => true,
            'analysis' => $analysis['data']
        ]);
    } else {
        // Fallback to local analysis
        $fallback = localAnalysis($prompt);
        echo json_encode([
            'success' => true,
            'analysis' => $fallback,
            'fallback' => true,
            'error' => $analysis['error'] ?? 'AI analysis failed'
        ]);
    }
    exit;
}

// Handle manual task analysis
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$dueDate = $data['due_date'] ?? date('Y-m-d');

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit;
}

$analysis = analyzeTaskLocal($title, $description, $dueDate);

echo json_encode([
    'success' => true,
    'task' => $analysis['task'],
    'ai_analysis' => $analysis['ai']
]);

// --- Helper Functions ---

function tryAIAnalysis($prompt) {
    error_log("tryAIAnalysis CALLED with length=" . strlen($prompt));
    $apiKey = 'AIzaSyDPWNWnNVBoX-FRq9qZbHOQe17wgf2OafM';
    $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
    
    // Simpler, more direct prompt
    // Focused academic prompt, same JSON fields
$systemPrompt = "You are an academic assistant for college students. The user will send you the FULL TEXT of an assignment or activity document.

Your job:
- Identify ONE main task the STUDENT must do.
- Ignore any meta-instructions like 'extract task info from this document', filenames, or tool descriptions.
- Focus ONLY on the actual assignment (what the student writes, answers, reads, or submits).

Return ONLY valid JSON with these fields:

{
    \"title\": \"Short, descriptive task title based on the assignment itself (e.g., 'Personal Narrative College Essay', 'JavaScript Formative Quiz', 'ENG 101 Personal Narrative Essay')\",
    \"activity_type\": \"homework, quiz, exam, project, essay, lab, reading, or other\",
    \"due_date\": \"YYYY-MM-DD format if a due date like 'March 15, 2026' is mentioned, otherwise null\",
    \"description\": \"1-2 sentence summary of what the student needs to do (for example: required output, topic, and length)\",
    \"priority\": \"high, medium, or low (high for exams/major projects/near deadlines, low for simple or non-urgent tasks)\",
    \"complexity\": 1-10 (higher for exams/major projects, lower for simple homework/reading)\",
    \"study_tips\": \"One short, practical study tip for this kind of task\"
}

Important:
- The title must reflect the assignment itself, not a generic label like 'Homework Task' or just the course code.
- The description MUST describe the actual assignment (e.g., 'Write a 600–800 word personal narrative essay responding to one of the prompts...'), NOT the tool instructions, filenames, or the literal phrase 'Document content:'.
- If no clear due date is in the document, set \"due_date\" to null.
- Always include all fields above with reasonable values.
- Respond with ONLY a single JSON object, nothing else.";

    
    $postData = json_encode([
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\n=== DOCUMENT ===\n" . $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 800,
            'topP' => 0.8,
            'topK' => 40
        ]
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Log for debugging
    error_log("API Response Code: " . $httpCode);
    error_log("cURL Error: " . ($curlError ?: 'none'));
    error_log("API Response: " . substr((string)$response, 0, 500));

    
    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        
        // Check for API errors
        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error']['message'] ?? 'API error'];
        }
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Try to extract JSON from response
            // Look for JSON object in the response
            if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                $json = json_decode($matches[0], true);
                if ($json && is_array($json)) {
                    // Validate required fields
                    if (!isset($json['title']) || empty($json['title'])) {
                        return ['success' => false, 'error' => 'Invalid response: no title'];
                    }
                    return ['success' => true, 'data' => $json];
                }
            }
            
        }
    }
    
    return ['success' => false, 'error' => 'Failed to get valid response from AI (HTTP ' . $httpCode . ')'];
}

function localAnalysis($text) {
    $textLower = strtolower($text);
    
    // Detect activity type
    $activityType = 'other';
    if (strpos($textLower, 'exam') !== false || strpos($textLower, 'final') !== false || strpos($textLower, 'midterm') !== false) {
        $activityType = 'exam';
    } elseif (strpos($textLower, 'quiz') !== false) {
        $activityType = 'quiz';
    } elseif (strpos($textLower, 'homework') !== false || strpos($textLower, 'hw') !== false) {
        $activityType = 'homework';
    } elseif (strpos($textLower, 'project') !== false) {
        $activityType = 'project';
    } elseif (strpos($textLower, 'essay') !== false || strpos($textLower, 'paper') !== false) {
        $activityType = 'essay';
    } elseif (strpos($textLower, 'lab') !== false) {
        $activityType = 'lab';
    } elseif (strpos($textLower, 'reading') !== false) {
        $activityType = 'reading';
    }
    
    // Detect keywords for title - more specific
    $title = 'Task from Document';
    $keywords = [
        'homework' => 'Homework Task',
        'assignment' => 'Assignment',
        'project' => 'Project',
        'essay' => 'Essay',
        'paper' => 'Paper',
        'quiz' => 'Quiz',
        'exam' => 'Exam',
        'midterm' => 'Midterm',
        'final' => 'Final Exam',
        'lab' => 'Lab Activity',
        'reading' => 'Reading Assignment',
        'chapter' => 'Chapter Review'
    ];
    foreach ($keywords as $kw => $titleGuess) {
        if (strpos($textLower, $kw) !== false) {
            $title = $titleGuess;
            break;
        }
    }
    
    // Try to extract subject/course name
    if (preg_match('/([A-Z]{2,4}\d{3}[A-Z]*|[A-Z]{2,4}\s+\d{3})/i', $text, $matches)) {
        $title = $matches[1] . ' ' . $title;
    }
    
    // Extract date - try multiple formats
    $dueDate = null;
    
    // Format: 2024-03-15 or 2024/03/15
    if (preg_match('/(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})/', $text, $m)) {
        $dueDate = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
    }
    // Format: 03/15/2024 or 3/15/24
    elseif (preg_match('/(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{2,4})/', $text, $m)) {
        $year = strlen($m[3]) === 2 ? (intval($m[3]) < 50 ? '20' : '19') . $m[3] : $m[3];
        $dueDate = sprintf('%04d-%02d-%02d', $year, $m[1], $m[2]);
    }
    // Format: March 15 or March 15th
    elseif (preg_match('/(january|february|march|april|may|june|july|august|september|october|november|december)\s+(\d{1,2})(st|nd|rd|th)?/i', $text, $m)) {
        $monthNum = date('m', strtotime($m[1] . ' 1'));
        $dueDate = date('Y') . '-' . $monthNum . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT);
    }
    // Format: 15th March or 15 March
    elseif (preg_match('/(\d{1,2})(st|nd|rd|th)?\s+(january|february|march|april|may|june|july|august|september|october|november|december)/i', $text, $m)) {
        $monthNum = date('m', strtotime($m[3] . ' 1'));
        $dueDate = date('Y') . '-' . $monthNum . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
    }
    // Look for "due" or "deadline" keywords
    elseif (preg_match('/due[:\s]+(january|february|march|april|may|june|july|august|september|october|november|december)\s+(\d{1,2})/i', $text, $m)) {
        $monthNum = date('m', strtotime($m[1] . ' 1'));
        $dueDate = date('Y') . '-' . $monthNum . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT);
    }
    elseif (preg_match('/deadline[:\s]+(january|february|march|april|may|june|july|august|september|october|november|december)\s+(\d{1,2})/i', $text, $m)) {
        $monthNum = date('m', strtotime($m[1] . ' 1'));
        $dueDate = date('Y') . '-' . $monthNum . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT);
    }
    
    // Calculate complexity
    $complexity = 3;
    if ($activityType === 'exam' || $activityType === 'project') $complexity = 7;
    elseif ($activityType === 'essay' || $activityType === 'lab') $complexity = 5;
    elseif ($activityType === 'quiz') $complexity = 4;
    
    $wordCount = str_word_count($text);
    if ($wordCount > 500) $complexity = min(10, $complexity + 2);
    elseif ($wordCount > 200) $complexity = min(10, $complexity + 1);
    
    // Determine priority
    $priority = 'medium';
    if ($complexity >= 7) $priority = 'high';
    elseif ($complexity <= 3) $priority = 'low';
    
    // Generate description - extract first meaningful content
    $description = substr($text, 0, 300);
    // Clean up the description
    $description = preg_replace('/\s+/', ' ', $description);
    if (strlen($text) > 300) $description .= '...';
    
    // Study tips
    $tips = 'Break this task into smaller chunks and study regularly.';
    if ($activityType === 'exam') $tips = 'Start reviewing early. Focus on key concepts and practice problems.';
    elseif ($activityType === 'project') $tips = 'Break into milestones. Start with research and outline first.';
    elseif ($activityType === 'essay') $tips = 'Start with an outline. Write in sections and review thoroughly.';
    elseif ($activityType === 'quiz') $tips = 'Review class notes and key concepts before the quiz.';
    
    return [
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate,
        'activity_type' => $activityType,
        'priority' => $priority,
        'complexity' => $complexity,
        'study_tips' => $tips
    ];
}

function analyzeTaskLocal($title, $description, $dueDate) {
    $wordCount = str_word_count($description);
    $titleLower = strtolower($title);
    
    // Calculate complexity from keywords
    $complexity = 3;
    if (strpos($titleLower, 'exam') !== false || strpos($titleLower, 'final') !== false || strpos($titleLower, 'midterm') !== false) {
        $complexity = 8;
    } elseif (strpos($titleLower, 'quiz') !== false) {
        $complexity = 5;
    } elseif (strpos($titleLower, 'project') !== false) {
        $complexity = 6;
    } elseif (strpos($titleLower, 'essay') !== false || strpos($titleLower, 'paper') !== false) {
        $complexity = 5;
    } elseif (strpos($titleLower, 'homework') !== false || strpos($titleLower, 'assignment') !== false) {
        $complexity = 4;
    }
    
    if ($wordCount > 500) $complexity = min(10, $complexity + 2);
    elseif ($wordCount > 200) $complexity = min(10, $complexity + 1);
    
    // Calculate days until due
    $due = new DateTime($dueDate);
    $today = new DateTime();
    $daysUntil = $due->diff($today)->days;
    
    // Determine priority
    $priority = 'medium';
    if ($daysUntil <= 3 || $complexity >= 7) $priority = 'high';
    elseif ($daysUntil > 7 && $complexity < 4) $priority = 'low';
    
    $task = [
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate,
        'priority' => $priority,
        'complexity' => $complexity,
        'insights' => 'Break this task into smaller chunks and study regularly.'
    ];
    
    $ai = [
        'priority' => $priority,
        'complexity' => $complexity,
        'word_count' => $wordCount,
        'days_until_due' => $daysUntil,
        'suggested_study_hours' => max(1, ceil($complexity / 2)),
        'recommended_profile' => $priority === 'high' ? 'early_crammer' : 'seamless',
        'tips' => 'Break this task into smaller chunks and study regularly.'
    ];
    
    return ['task' => $task, 'ai' => $ai];
}


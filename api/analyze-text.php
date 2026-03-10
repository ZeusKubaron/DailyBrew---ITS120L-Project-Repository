<?php
/**
 * Analyze Text API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Analyzes text input to extract tasks/deadlines
 * Uses fallback analysis when AI is unavailable
 */

// Disable all error output
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

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
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$dueDate = $data['due_date'] ?? date('Y-m-d');

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
            'fallback' => true
        ]);
    }
    exit;
}

// Handle manual task analysis
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
    $apiKey = 'AIzaSyDPWNWnNVBoX-FRq9qZbHOQe17wgf2OafM';
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
    
    $systemPrompt = "You are a helpful AI assistant for students. Analyze the following academic task document and extract key information. Return ONLY a JSON object with these fields:
{
    \"title\": \"A short descriptive title for the task\",
    \"description\": \"A brief 1-2 sentence summary of the main requirements\",
    \"due_date\": \"The due date in YYYY-MM-DD format if found, otherwise null\",
    \"activity_type\": \"homework, quiz, exam, project, essay, lab, reading, or other\",
    \"priority\": \"high, medium, or low based on urgency\",
    \"complexity\": 1-10 estimate based on difficulty keywords,
    \"study_tips\": \"A brief study tip for this type of task\"
}

Only respond with JSON, no other text.";

    $postData = json_encode([
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nDocument content:\n" . $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 500
        ]
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Try to extract JSON from response
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $json = json_decode($matches[0], true);
                if ($json && is_array($json)) {
                    return ['success' => true, 'data' => $json];
                }
            }
        }
    }
    
    return ['success' => false, 'error' => 'AI unavailable'];
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
    
    // Detect keywords for title
    $title = 'Task from Document';
    $keywords = ['homework', 'assignment', 'project', 'essay', 'paper', 'quiz', 'exam', 'midterm', 'final', 'lab', 'reading', 'chapter'];
    foreach ($keywords as $kw) {
        if (strpos($textLower, $kw) !== false) {
            $title = ucfirst($kw) . ' Task';
            break;
        }
    }
    
    // Extract date if mentioned
    $dueDate = null;
    if (preg_match('/(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})/', $text, $m)) {
        $dueDate = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
    } elseif (preg_match('/(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{2,4})/', $text, $m)) {
        $year = strlen($m[3]) === 2 ? '20' . $m[3] : $m[3];
        $dueDate = sprintf('%04d-%02d-%02d', $year, $m[1], $m[2]);
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
    
    // Generate description
    $description = substr($text, 0, 200);
    if (strlen($text) > 200) $description .= '...';
    
    // Study tips
    $tips = 'Break this task into smaller chunks and study regularly.';
    if ($activityType === 'exam') $tips = 'Start reviewing early. Focus on key concepts and practice problems.';
    elseif ($activityType === 'project') $tips = 'Break into milestones. Start with research and outline first.';
    elseif ($activityType === 'essay') $tips = 'Start with an outline. Write in sections and review thoroughly.';
    
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


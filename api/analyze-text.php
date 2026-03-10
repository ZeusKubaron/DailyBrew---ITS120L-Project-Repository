<?php
/**
 * Analyze Text API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Analyzes manual text input to extract tasks/deadlines
 * Also handles document content analysis
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
$prompt = $data['prompt'] ?? null;
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$dueDate = $data['due_date'] ?? date('Y-m-d');

// Handle document analysis via prompt
if ($prompt) {
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
    $analysis = [
        'title' => '',
        'description' => '',
        'due_date' => null,
        'activity_type' => 'other',
        'priority' => 'medium',
        'complexity' => 5,
        'study_tips' => 'Break this task into smaller chunks and study regularly.'
    ];
    
    // Try to extract JSON from response
    if (preg_match('/\{.*\}/s', $text, $matches)) {
        $json = json_decode($matches[0], true);
        if ($json) {
            $analysis = array_merge($analysis, $json);
        }
    }
    
    echo json_encode([
        'success' => true,
        'analysis' => $analysis
    ]);
    exit;
}

// Handle manual task analysis (original functionality)
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit;
}

// Use the built-in analyzeTask function
$analysis = analyzeTask($title, $description, $dueDate);

echo json_encode([
    'success' => true,
    'task' => [
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate,
        'due_time' => $data['due_time'] ?? null,
        'priority' => $analysis['priority'],
        'complexity' => $analysis['complexity'],
        'insights' => $analysis['tips'] ?? 'Break this task into smaller chunks and study regularly.'
    ],
    'ai_analysis' => $analysis
]);


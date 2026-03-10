<?php
/**
 * Analyze Text API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Analyzes manual text input to extract tasks/deadlines
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
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$dueDate = $data['due_date'] ?? date('Y-m-d');

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


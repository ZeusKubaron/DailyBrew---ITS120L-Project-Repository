<?php
/**
 * Gemini Chat API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
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
$message = $data['message'] ?? '';

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Empty message']);
    exit;
}

// Get user context
$user = getCurrentUser();
$userId = $user['id'];

// Get user's tasks and schedule for context
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND status != 'completed' ORDER BY due_date ASC LIMIT 5");
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM study_blocks WHERE user_id = ? AND scheduled_date >= CURDATE() ORDER BY scheduled_date, start_time LIMIT 10");
$stmt->execute([$userId]);
$studyBlocks = $stmt->fetchAll();

$preferences = getUserPreferences($userId);

// Build context
$context = "User: " . $user['first_name'] . " " . $user['last_name'] . "\n";
$context .= "Upcoming Tasks:\n";
foreach ($tasks as $task) {
    $priority = $task['user_priority'] ?? $task['ai_priority'];
    $context .= "- " . $task['title'] . " (Due: " . $task['due_date'] . ", Priority: $priority)\n";
}

$context .= "\nStudy Blocks:\n";
foreach ($studyBlocks as $block) {
    $context .= "- " . $block['title'] . " on " . $block['scheduled_date'] . " at " . $block['start_time'] . "\n";
}

$context .= "\nUser Preferences:\n";
$context .= "- Study blocks: " . ($preferences['study_block_duration'] ?? 30) . " minutes\n";
$context .= "- Available: " . ($preferences['earliest_time_start'] ?? '08:00') . " to " . ($preferences['latest_time_end'] ?? '22:00') . "\n";
$context .= "- Default Profile: " . ($preferences['default_profile'] ?? 'seamless') . "\n";

$fullPrompt = $context . "\n\nUser Question: " . $message . "\n\nPlease provide a helpful response about their schedule, tasks, or study planning.";

$result = callGeminiAPI($fullPrompt);

if ($result && $result['success']) {
    echo json_encode([
        'success' => true,
        'response' => $result['text']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $result['error'] ?? 'Failed to get response from AI'
    ]);
}


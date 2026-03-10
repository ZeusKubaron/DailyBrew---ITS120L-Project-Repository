<?php
/**
 * Schedule Task API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 * 
 * Creates a new task and generates AI-powered study blocks
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/gemini.php';
require_once __DIR__ . '/../auth/session.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$user = getCurrentUser();
$userId = $user['id'];

// Handle both POST and GET for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
} else {
    $data = $_GET;
}

// Validate required fields
$title = trim($data['title'] ?? '');
$dueDate = $data['due_date'] ?? '';
$description = trim($data['description'] ?? '');
$dueTime = $data['due_time'] ?? null;
$profile = $data['profile'] ?? 'seamless';

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Task title is required']);
    exit;
}

if (empty($dueDate)) {
    echo json_encode(['success' => false, 'error' => 'Due date is required']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
    echo json_encode(['success' => false, 'error' => 'Invalid date format']);
    exit;
}

try {
    // Analyze task with AI
    $analysis = analyzeTask($title, $description, $dueDate);
    
    // Create task in database
    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, title, description, due_date, due_time, ai_priority, complexity, profile, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $userId,
        $title,
        $description,
        $dueDate,
        $dueTime,
        $analysis['priority'],
        $analysis['complexity'],
        $profile
    ]);
    
    $taskId = $pdo->lastInsertId();
    
    // Get user preferences
    $preferences = getUserPreferences($userId);
    
    // Get academic schedule
    $stmt = $pdo->prepare("SELECT * FROM academic_schedule WHERE user_id = ?");
    $stmt->execute([$userId]);
    $schedule = $stmt->fetchAll();
    
    // Get existing study blocks
    $stmt = $pdo->prepare("SELECT * FROM study_blocks WHERE user_id = ? AND scheduled_date >= CURDATE()");
    $stmt->execute([$userId]);
    $existingBlocks = $stmt->fetchAll();
    
    // Generate study blocks
    $taskData = [
        'id' => $taskId,
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate,
        'complexity' => $analysis['complexity'],
        'profile' => $profile
    ];
    
    $generatedBlocks = generateStudyBlocks($userId, $taskId, $taskData, $preferences, $schedule, $existingBlocks);
    
    // Insert generated study blocks
    $blocksCreated = 0;
    foreach ($generatedBlocks as $block) {
        $stmt = $pdo->prepare("
            INSERT INTO study_blocks (user_id, task_id, title, scheduled_date, start_time, end_time, profile)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $block['user_id'],
            $block['task_id'],
            $block['title'],
            $block['scheduled_date'],
            $block['start_time'],
            $block['end_time'],
            $block['profile']
        ]);
        
        $blocksCreated++;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Task created successfully!',
        'task' => [
            'id' => $taskId,
            'title' => $title,
            'due_date' => $dueDate,
            'ai_priority' => $analysis['priority'],
            'complexity' => $analysis['complexity'],
            'suggested_hours' => $analysis['suggested_study_hours'] ?? ceil($analysis['complexity'] / 2)
        ],
        'study_blocks' => [
            'created' => $blocksCreated,
            'blocks' => $generatedBlocks
        ],
        'ai_analysis' => $analysis
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create task: ' . $e->getMessage()
    ]);
}


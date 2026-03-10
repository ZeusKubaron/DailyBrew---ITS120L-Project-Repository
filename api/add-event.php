<?php
/**
 * Add Event API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
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
$userId = getCurrentUserId();

$eventType = $data['event_type'] ?? 'task';
$title = trim($data['title'] ?? '');
$date = $data['date'] ?? date('Y-m-d');
$time = $data['time'] ?? null;
$description = trim($data['description'] ?? '');

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit;
}

try {
    switch ($eventType) {
        case 'task':
            $dueTime = $time ?: null;
            $stmt = $pdo->prepare("
                INSERT INTO tasks (user_id, title, description, due_date, due_time, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$userId, $title, $description, $date, $dueTime]);
            $id = $pdo->lastInsertId();
            
            // Analyze with AI and create study blocks if needed
            require_once __DIR__ . '/../config/gemini.php';
            $analysis = analyzeTask($title, $description, $date);
            
            $stmt = $pdo->prepare("UPDATE tasks SET ai_priority = ?, complexity = ? WHERE id = ?");
            $stmt->execute([$analysis['priority'], $analysis['complexity'], $id]);
            break;
            
        case 'study':
            $endTime = $data['end_time'] ?? date('H:i:s', strtotime($time . ' +1 hour'));
            $stmt = $pdo->prepare("
                INSERT INTO study_blocks (user_id, title, scheduled_date, start_time, end_time, profile)
                VALUES (?, ?, ?, ?, ?, 'seamless')
            ");
            $stmt->execute([$userId, $title, $date, $time, $endTime]);
            $id = $pdo->lastInsertId();
            break;
            
        case 'class':
            $dayOfWeek = $data['day_of_week'] ?? date('l', strtotime($date));
            $startTime = $time ?? '09:00:00';
            $endTime = $data['end_time'] ?? date('H:i:s', strtotime($startTime . ' +1 hour'));
            $location = $data['location'] ?? '';
            
            $stmt = $pdo->prepare("
                INSERT INTO academic_schedule (user_id, subject, day_of_week, start_time, end_time, location)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $title, $dayOfWeek, $startTime, $endTime, $location]);
            $id = $pdo->lastInsertId();
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid event type']);
            exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Event added successfully',
        'event_id' => $id,
        'event_type' => $eventType
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


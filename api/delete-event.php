<?php
/**
 * Delete Event API Endpoint
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

$type = $data['type'] ?? '';
$id = $data['id'] ?? 0;

if (empty($type) || empty($id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

try {
    switch ($type) {
        case 'task':
            // Also delete associated study blocks
            $stmt = $pdo->prepare("DELETE FROM study_blocks WHERE task_id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            break;
            
        case 'study_block':
            $stmt = $pdo->prepare("DELETE FROM study_blocks WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            break;
            
        case 'class':
            $stmt = $pdo->prepare("DELETE FROM academic_schedule WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid event type']);
            exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


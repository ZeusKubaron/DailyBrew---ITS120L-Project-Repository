<?php
/**
 * Update Event API Endpoint
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
            $dueDate = $data['date'] ?? null;
            $dueTime = $data['time'] ?? null;
            $status = $data['status'] ?? null;
            $userPriority = $data['user_priority'] ?? null;
            
            $updates = [];
            $params = [];
            
            if ($dueDate) {
                $updates[] = 'due_date = ?';
                $params[] = $dueDate;
            }
            if ($dueTime !== null) {
                $updates[] = 'due_time = ?';
                $params[] = $dueTime;
            }
            if ($status) {
                $updates[] = 'status = ?';
                $params[] = $status;
            }
            if ($userPriority) {
                $updates[] = 'user_priority = ?';
                $params[] = $userPriority;
            }
            
            if (!empty($updates)) {
                $params[] = $id;
                $params[] = $userId;
                $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            break;
            
        case 'study_block':
            $scheduledDate = $data['date'] ?? null;
            $startTime = $data['start_time'] ?? null;
            $endTime = $data['end_time'] ?? null;
            $title = $data['title'] ?? null;
            
            $updates = [];
            $params = [];
            
            if ($scheduledDate) {
                $updates[] = 'scheduled_date = ?';
                $params[] = $scheduledDate;
            }
            if ($startTime) {
                $updates[] = 'start_time = ?';
                $params[] = $startTime;
            }
            if ($endTime) {
                $updates[] = 'end_time = ?';
                $params[] = $endTime;
            }
            if ($title) {
                $updates[] = 'title = ?';
                $params[] = $title;
            }
            
            if (!empty($updates)) {
                $params[] = $id;
                $params[] = $userId;
                $sql = "UPDATE study_blocks SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            break;
            
        case 'class':
            $dayOfWeek = $data['day_of_week'] ?? null;
            $startTime = $data['start_time'] ?? null;
            $endTime = $data['end_time'] ?? null;
            $subject = $data['title'] ?? null;
            $location = $data['location'] ?? null;
            
            $updates = [];
            $params = [];
            
            if ($dayOfWeek) {
                $updates[] = 'day_of_week = ?';
                $params[] = $dayOfWeek;
            }
            if ($startTime) {
                $updates[] = 'start_time = ?';
                $params[] = $startTime;
            }
            if ($endTime) {
                $updates[] = 'end_time = ?';
                $params[] = $endTime;
            }
            if ($subject) {
                $updates[] = 'subject = ?';
                $params[] = $subject;
            }
            if ($location !== null) {
                $updates[] = 'location = ?';
                $params[] = $location;
            }
            
            if (!empty($updates)) {
                $params[] = $id;
                $params[] = $userId;
                $sql = "UPDATE academic_schedule SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid event type']);
            exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


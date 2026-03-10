<?php
/**
 * Get Events API Endpoint
 * DailyBrew - AI-Assisted Student Scheduler
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/session.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$userId = getCurrentUserId();
$type = $_GET['type'] ?? 'all';
$start = $_GET['start'] ?? date('Y-m-d');
$end = $_GET['end'] ?? date('Y-m-d', strtotime('+30 days'));

$events = [];

try {
    // Get Tasks
    if ($type === 'all' || $type === 'tasks') {
        $stmt = $pdo->prepare("
            SELECT id, title, description, due_date, due_time, ai_priority, user_priority, complexity, status
            FROM tasks 
            WHERE user_id = ? AND due_date BETWEEN ? AND ?
            ORDER BY due_date, due_time
        ");
        $stmt->execute([$userId, $start, $end]);
        $tasks = $stmt->fetchAll();
        
        foreach ($tasks as $task) {
            $events['tasks'][] = [
                'id' => $task['id'],
                'title' => $task['title'],
                'description' => $task['description'],
                'due_date' => $task['due_date'],
                'due_time' => $task['due_time'],
                'ai_priority' => $task['user_priority'] ?? $task['ai_priority'],
                'complexity' => $task['complexity'],
                'status' => $task['status']
            ];
        }
    }
    
    // Get Study Blocks
    if ($type === 'all' || $type === 'study_blocks') {
        $stmt = $pdo->prepare("
            SELECT id, task_id, title, scheduled_date, start_time, end_time, profile, notes
            FROM study_blocks 
            WHERE user_id = ? AND scheduled_date BETWEEN ? AND ?
            ORDER BY scheduled_date, start_time
        ");
        $stmt->execute([$userId, $start, $end]);
        $blocks = $stmt->fetchAll();
        
        foreach ($blocks as $block) {
            $events['study_blocks'][] = [
                'id' => $block['id'],
                'task_id' => $block['task_id'],
                'title' => $block['title'],
                'scheduled_date' => $block['scheduled_date'],
                'start_time' => $block['start_time'],
                'end_time' => $block['end_time'],
                'profile' => $block['profile'],
                'notes' => $block['notes']
            ];
        }
    }
    
    // Get Academic Schedule
    if ($type === 'all' || $type === 'schedule') {
        $stmt = $pdo->prepare("
            SELECT id, day_of_week, start_time, end_time, subject, location, color
            FROM academic_schedule 
            WHERE user_id = ?
            ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time
        ");
        $stmt->execute([$userId]);
        $schedule = $stmt->fetchAll();
        
        $dayMap = [
            'Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
            'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6
        ];
        
        foreach ($schedule as $cls) {
            $events['schedule'][] = [
                'id' => $cls['id'],
                'day_of_week' => $cls['day_of_week'],
                'day_of_week_index' => $dayMap[$cls['day_of_week']],
                'start_time' => $cls['start_time'],
                'end_time' => $cls['end_time'],
                'subject' => $cls['subject'],
                'location' => $cls['location'],
                'color' => $cls['color']
            ];
        }
    }
    
    echo json_encode(['success' => true, 'events' => $events]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


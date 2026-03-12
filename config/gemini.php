<?php
/**
 * Gemini API Configuration
 * DailyBrew - AI-Assisted Student Scheduler
 */

// Gemini API Configuration
define('GEMINI_API_KEY', 'AIzaSyDPWNWnNVBoX-FRq9qZbHOQe17wgf2OafM');
define('GEMINI_MODEL', 'gemini-2.0-flash');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');

/**
 * Send request to Gemini API
 * 
 * @param string $prompt The prompt to send to Gemini
 * @param array $history Optional conversation history
 * @return array|null Response from Gemini or null on failure
 */
function callGeminiAPI($prompt, $history = []) {
    $url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;
    
    // Build messages array
    $messages = [];
    
    // Add system prompt
    $systemPrompt = "You are an AI scheduling assistant for a student calendar app called DailyBrew. 
Your role is to help students schedule their tasks and deadlines effectively. 
You analyze task descriptions to determine complexity and priority.
You generate study block schedules based on three profiles:
- Early Crammer: Schedule as early as possible
- Seamless: Even distribution with allowance between activities
- Late Crammer: Schedule as late as possible (before deadline)

Provide helpful, concise responses about scheduling, task prioritization, and study planning.";
    
    $messages[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt]]];
    
    // Add conversation history
    foreach ($history as $msg) {
        $messages[] = $msg;
    }
    
    // Add current prompt
    $messages[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];
    
    $data = [
        'contents' => $messages,
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 2048,
            'topP' => 0.8,
            'topK' => 40,
            'response_mime_type' => 'application/json'
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'text' => $result['candidates'][0]['content']['parts'][0]['text']
            ];
        } elseif (isset($result['error'])) {
            return [
                'success' => false,
                'error' => $result['error']['message'] ?? 'Unknown error'
            ];
        }
    }
    
    return ['success' => false, 'error' => 'Failed to connect to Gemini API'];
}

/**
 * Analyze task and determine priority and complexity
 * 
 * @param string $title Task title
 * @param string $description Task description
 * @param string $dueDate Due date
 * @return array Priority and complexity analysis
 */
function analyzeTask($title, $description, $dueDate) {
    $wordCount = str_word_count($description);
    
    // Calculate base complexity from keywords
    $keywords = [
        'exam' => 5, 'final' => 5, 'midterm' => 5, 'finals' => 5,
        'quiz' => 3, 'quizzes' => 3,
        'sa' => 3, 'fa' => 3, 'short' => 3,
        'homework' => 2, 'hw' => 2, 'assignment' => 2, 'task' => 2,
        'project' => 4, 'paper' => 3, 'essay' => 3,
        'reading' => 2, 'chapter' => 1
    ];
    
    $titleLower = strtolower($title);
    $keywordScore = 0;
    
    foreach ($keywords as $keyword => $score) {
        if (strpos($titleLower, $keyword) !== false) {
            $keywordScore = max($keywordScore, $score);
        }
    }
    
    // Word count contribution
    if ($wordCount < 100) {
        $wordScore = 1;
    } elseif ($wordCount < 500) {
        $wordScore = 2;
    } else {
        $wordScore = 3;
    }
    
    $complexity = min(10, $keywordScore + $wordScore);
    
    // Calculate days until due
    $due = new DateTime($dueDate);
    $today = new DateTime();
    $daysUntilDue = $due->diff($today)->days;
    
    // Determine priority based on days and complexity
    if ($daysUntilDue <= 3 || $complexity >= 8) {
        $priority = 'high';
    } elseif ($daysUntilDue <= 7 || $complexity >= 5) {
        $priority = 'medium';
    } else {
        $priority = 'low';
    }
    
    // Use AI for more detailed analysis
    $prompt = "Analyze this academic task:
Title: $title
Description: $description
Due Date: $dueDate

Provide a JSON response with:
- priority: 'high', 'medium', or 'low'
- complexity: 1-10
- suggested_study_hours: estimated hours needed
- recommended_profile: 'early_crammer', 'seamless', or 'late_crammer'
- tips: brief study tips for this task

Respond only in JSON format.";
    
    $aiResult = callGeminiAPI($prompt);
    
    if ($aiResult && $aiResult['success']) {
        // Try to extract JSON from response
        $text = $aiResult['text'];
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $json = json_decode($matches[0], true);
            if ($json) {
                return array_merge([
                    'priority' => $priority,
                    'complexity' => $complexity,
                    'word_count' => $wordCount,
                    'days_until_due' => $daysUntilDue
                ], $json);
            }
        }
    }
    
    return [
        'priority' => $priority,
        'complexity' => $complexity,
        'word_count' => $wordCount,
        'days_until_due' => $daysUntilDue,
        'suggested_study_hours' => max(1, ceil($complexity / 2)),
        'recommended_profile' => $priority === 'high' ? 'early_crammer' : 'seamless',
        'tips' => 'Break this task into smaller chunks and study regularly.'
    ];
}

/**
 * Generate study blocks for a task
 * 
 * @param int $userId User ID
 * @param int $taskId Task ID
 * @param array $task Task data
 * @param array $preferences User preferences
 * @param array $schedule Academic schedule
 * @param array $existingBlocks Existing study blocks
 * @return array Generated study blocks
 */
function generateStudyBlocks($userId, $taskId, $task, $preferences, $schedule, $existingBlocks) {
    $blocks = [];
    
    $duration = $preferences['study_block_duration'] ?? 30;
    $earliestStart = strtotime($preferences['earliest_time_start'] ?? '08:00');
    $latestEnd = strtotime($preferences['latest_time_end'] ?? '22:00');
    $profile = $task['profile'] ?? $preferences['default_profile'] ?? 'seamless';
    
    $dueDate = new DateTime($task['due_date']);
    $today = new DateTime();
    $daysUntilDue = $dueDate->diff($today)->days;
    
    // Calculate total study time needed (1 hour per complexity point)
    $totalHours = max(1, $task['complexity'] ?? 5);
    $blockCount = ceil($totalHours * 60 / $duration);
    
    // Determine available date range based on profile
    switch ($profile) {
        case 'early_crammer':
            $startDate = clone $today;
            $endDate = (clone $dueDate)->modify('-1 day');
            break;
        case 'late_crammer':
            $startDate = (clone $dueDate)->modify('-3 days');
            if ($startDate < $today) $startDate = $today;
            $endDate = (clone $dueDate)->modify('-1 day');
            break;
        case 'seamless':
        default:
            $startDate = clone $today;
            $endDate = (clone $dueDate)->modify('-2 days');
            break;
    }
    
    if ($endDate < $startDate) {
        $endDate = $startDate;
    }
    
    // Find available time slots
    $availableSlots = [];
    $currentDate = clone $startDate;
    
    while ($currentDate <= $endDate) {
        $dayOfWeek = $currentDate->format('l');
        
        // Get occupied slots for this day
        $occupied = getOccupiedSlots($schedule, $existingBlocks, $currentDate->format('Y-m-d'));
        
        // Find free slots
        $slotStart = $earliestStart;
        while ($slotStart + ($duration * 60) <= $latestEnd) {
            $slotEnd = $slotStart + ($duration * 60 * 60);
            
            if (!isSlotOccupied($occupied, $slotStart, $slotEnd)) {
                $availableSlots[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'start' => $slotStart,
                    'end' => $slotEnd
                ];
            }
            
            $slotStart += ($duration * 60 * 60); // Next slot
        }
        
        $currentDate->modify('+1 day');
    }
    
    // Assign study blocks from available slots
    $blocksCreated = 0;
    foreach ($availableSlots as $slot) {
        if ($blocksCreated >= $blockCount) break;
        
        $blocks[] = [
            'user_id' => $userId,
            'task_id' => $taskId,
            'title' => 'Study: ' . $task['title'],
            'scheduled_date' => $slot['date'],
            'start_time' => date('H:i:s', $slot['start']),
            'end_time' => date('H:i:s', $slot['end']),
            'profile' => $profile
        ];
        
        $blocksCreated++;
    }
    
    return $blocks;
}

/**
 * Get occupied time slots for a specific date
 */
function getOccupiedSlots($schedule, $blocks, $date) {
    $occupied = [];
    $dayOfWeek = date('l', strtotime($date));
    
    // Add academic schedule
    foreach ($schedule as $s) {
        if ($s['day_of_week'] === $dayOfWeek) {
            $occupied[] = [
                'start' => strtotime($s['start_time']),
                'end' => strtotime($s['end_time']),
                'type' => 'class'
            ];
        }
    }
    
    // Add existing study blocks
    foreach ($blocks as $b) {
        if ($b['scheduled_date'] === $date) {
            $occupied[] = [
                'start' => strtotime($b['start_time']),
                'end' => strtotime($b['end_time']),
                'type' => 'study'
            ];
        }
    }
    
    return $occupied;
}

/**
 * Check if a time slot is occupied
 */
function isSlotOccupied($occupied, $slotStart, $slotEnd) {
    foreach ($occupied as $slot) {
        if (($slotStart >= $slot['start'] && $slotStart < $slot['end']) ||
            ($slotEnd > $slot['start'] && $slotEnd <= $slot['end']) ||
            ($slotStart <= $slot['start'] && $slotEnd >= $slot['end'])) {
            return true;
        }
    }
    return false;
}


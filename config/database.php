<?php
/**
 * DailyBrew - No Database Version
 * Uses localStorage in browser for data storage
 * This file just initializes the PHP session
 */

session_start();

// Helper functions that work with localStorage via JS
// These are placeholder functions for compatibility
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getCurrentUser() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function getUserPreferences($userId = null) {
    return [
        'earliest_time_start' => '08:00:00',
        'latest_time_end' => '22:00:00',
        'study_block_duration' => 30,
        'default_profile' => 'seamless'
    ];
}


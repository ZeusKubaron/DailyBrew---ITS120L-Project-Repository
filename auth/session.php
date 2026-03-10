<?php
/**
 * Session Management
 * DailyBrew - No database version
 */

session_start();

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['user']);
}

// Helper function to get current user ID
function getCurrentUserId() {
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    return null;
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit;
    }
}


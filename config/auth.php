<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin";
}

function isUser() {
    return isLoggedIn() && isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "user";
}

function getCurrentUserId() {
    return $_SESSION["user_id"] ?? null;
}

function getCurrentUsername() {
    return $_SESSION["username"] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION["user_role"] ?? null;
}

function requireLogin($redirect_path = "../pages/login.php?error=unauthorized") {
    if (!isLoggedIn()) {
        header("Location: " . $redirect_path);
        exit;
    }
}

function requireAdmin($redirect_path = "../pages/login.php?error=forbidden") {
    if (!isAdmin()) {
        // Jika sudah login tapi bukan admin, mungkin arahkan ke dashboard user?
        if (isUser()) {
             header("Location: ../pages/users/user_dashboard.php?error=forbidden");
        } else {
             header("Location: " . $redirect_path);
        }
        exit;
    }
}

function requireUser($redirect_path = "../pages/login.php?error=forbidden") {
    if (!isUser()) {
        // Jika sudah login tapi bukan user (misal admin), arahkan ke dashboard admin?
        if (isAdmin()) {
             header("Location: ../pages/admin/admin_dashboard.php?error=forbidden");
        } else {
             header("Location: " . $redirect_path);
        }
        exit;
    }
}

function clearNotificationSession() {
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    unset($_SESSION['book_success_message']);
    unset($_SESSION['book_error_message']);
}
?>

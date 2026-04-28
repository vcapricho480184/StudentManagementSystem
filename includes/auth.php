<?php
/**
 * Authentication Helper
 * 
 * Manages session-based authentication.
 * Include this file on every protected page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is logged in.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get the logged-in admin's full name.
 */
function getAdminName(): string
{
    return $_SESSION['admin_name'] ?? 'Admin';
}

/**
 * Get the logged-in admin's username.
 */
function getAdminUsername(): string
{
    return $_SESSION['admin_username'] ?? '';
}

/**
 * Require authentication — redirects to login if not authenticated.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Set a flash message in the session.
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Get and clear the flash message.
 */
function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

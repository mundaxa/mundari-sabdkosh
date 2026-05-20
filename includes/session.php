<?php
require_once __DIR__ . '/config.php';

class SessionManager {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            session_unset();
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        session_unset();
        session_destroy();
    }

    public static function setFlash($key, $message) {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash($key, $default = null) {
        $message = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    public static function hasFlash($key) {
        return isset($_SESSION['_flash'][$key]);
    }
}

SessionManager::init();

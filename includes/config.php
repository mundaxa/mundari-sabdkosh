<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'mundari_sabdkosh');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Mundari Sabdkosh');
define('SITE_TAGLINE', 'Tribal Dictionary & Knowledge System');
define('SITE_URL', 'http://localhost/mundari-sabdkosh');
define('SITE_EMAIL', 'admin@mundarisabdkosh.org');
define('SITE_VERSION', '1.0.0');

define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/mundari-sabdkosh/assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('MAX_UPLOAD_SIZE', 52428800);
define('ITEMS_PER_PAGE', 20);
define('SESSION_LIFETIME', 86400);

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../system/error.log');

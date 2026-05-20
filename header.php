<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentUser = null;
$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $currentUser = \Auth::user();
    $unreadCount = unreadNotificationCount($_SESSION['user_id']);
}
$siteName = getSetting('site_name', SITE_NAME);
$siteTagline = getSetting('site_tagline', SITE_TAGLINE);
$theme = $_COOKIE['theme'] ?? 'dark';
$pageTitle = $pageTitle ?? $siteName;
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo escape($siteTagline); ?>">
    <meta name="theme-color" content="#0a0b14">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title><?php echo escape($pageTitle); ?></title>

    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%234f7eff'/%3E%3Ctext x='50' y='68' font-size='50' text-anchor='middle' fill='white' font-family='serif'%3Eम%3C/text%3E%3C/svg%3E">
    <link rel="apple-touch-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%234f7eff'/%3E%3Ctext x='50' y='68' font-size='50' text-anchor='middle' fill='white' font-family='serif'%3Eम%3C/text%3E%3C/svg%3E">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark.css">
    <link rel="stylesheet" href="assets/css/light.css">
    <link rel="stylesheet" href="assets/css/responsive.css">

    <style>
        .custom-tooltip {
            position: fixed;
            background: var(--bg-elevated);
            color: var(--text-primary);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 500;
            pointer-events: none;
            z-index: 9999;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            white-space: nowrap;
        }
        .search-suggestions {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 500;
            display: none;
            overflow: hidden;
        }
        .search-suggestions .ss-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            transition: var(--transition);
            cursor: pointer;
        }
        .search-suggestions .ss-item:hover {
            background: var(--bg-hover);
        }
        .search-suggestions .ss-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .search-suggestions .ss-word {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .search-suggestions .ss-meaning {
            font-size: 12px;
            color: var(--text-tertiary);
        }
        .search-suggestions .ss-category {
            font-size: 11px;
            font-weight: 500;
        }
        .search-suggestions .ss-all {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            font-size: 12px;
            font-weight: 500;
            color: var(--accent-primary);
            border-top: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .search-suggestions .ss-all:hover {
            background: var(--bg-hover);
        }
        .search-suggestions .ss-empty {
            padding: 16px;
            text-align: center;
            color: var(--text-tertiary);
            font-size: 13px;
        }
        .navbar-search.focused .search-suggestions {
            display: block;
        }
        .wod-audio-btn.playing i {
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .voice-search-btn.listening {
            color: var(--error) !important;
            animation: pulse 0.5s infinite;
        }
        [data-reveal] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        [data-reveal].revealed {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

<div class="mobile-overlay"></div>

<div class="app-layout">

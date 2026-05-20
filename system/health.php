<?php
header('Content-Type: application/json; charset=utf-8');

$checks = [];
$allOk = true;

// PHP Version
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '8.0', '>=');
$checks[] = ['name' => 'PHP Version', 'status' => $phpOk ? 'OK' : 'FAIL', 'value' => $phpVersion];
if (!$phpOk) $allOk = false;

// PDO Extension
$pdoOk = extension_loaded('pdo_mysql');
$checks[] = ['name' => 'PDO MySQL', 'status' => $pdoOk ? 'OK' : 'FAIL', 'value' => $pdoOk ? 'Loaded' : 'Missing'];
if (!$pdoOk) $allOk = false;

// Database Connection
try {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db.php';
    $db = db();
    $db->query('SELECT 1');
    $checks[] = ['name' => 'Database Connection', 'status' => 'OK', 'value' => DB_NAME];
} catch (Exception $e) {
    $checks[] = ['name' => 'Database Connection', 'status' => 'FAIL', 'value' => $e->getMessage()];
    $allOk = false;
}

// GD Library
$gdOk = extension_loaded('gd');
$checks[] = ['name' => 'GD Library', 'status' => $gdOk ? 'OK' : 'WARN', 'value' => $gdOk ? 'Loaded' : 'Missing (image processing limited)'];

// mbstring
$mbOk = extension_loaded('mbstring');
$checks[] = ['name' => 'mbstring', 'status' => $mbOk ? 'OK' : 'FAIL', 'value' => $mbOk ? 'Loaded' : 'Missing'];
if (!$mbOk) $allOk = false;

// Upload Directories
$dirs = ['assets/uploads', 'assets/audio', 'assets/video'];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/../' . $dir;
    $writable = is_dir($path) && is_writable($path);
    $checks[] = ['name' => "Directory: $dir", 'status' => $writable ? 'OK' : 'FAIL', 'value' => $writable ? 'Writable' : 'Not writable'];
    if (!$writable) $allOk = false;
}

// Config file
$configOk = file_exists(__DIR__ . '/../includes/config.php');
$checks[] = ['name' => 'Config File', 'status' => $configOk ? 'OK' : 'FAIL', 'value' => $configOk ? 'Found' : 'Missing'];
if (!$configOk) $allOk = false;

echo json_encode([
    'status' => $allOk ? 'healthy' : 'issues_found',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => $checks
], JSON_PRETTY_PRINT);

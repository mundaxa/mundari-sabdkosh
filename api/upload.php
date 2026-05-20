<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['file'])) {
    jsonResponse(['error' => 'No file uploaded'], 400);
}

$file = $_FILES['file'];
$type = $_POST['type'] ?? 'image';

$allowedTypes = ['image', 'audio', 'video', 'document'];
if (!in_array($type, $allowedTypes)) {
    jsonResponse(['error' => 'Invalid file type'], 400);
}

$result = uploadFile($file, $type);

if (!$result) {
    jsonResponse(['error' => 'File upload failed. Check file type and size.'], 400);
}

try {
    $db = db();
    $stmt = $db->prepare("INSERT INTO uploads (user_id, filename, original_name, filepath, filesize, filetype, mime_type, media_type, status)
                          VALUES (:uid, :filename, :original, :filepath, :size, :ftype, :mime, :mtype, 'pending')");
    $stmt->execute([
        'uid' => $_SESSION['user_id'],
        'filename' => $result['filename'],
        'original' => $result['original_name'],
        'filepath' => $result['filepath'],
        'size' => $result['filesize'],
        'ftype' => $result['filetype'],
        'mime' => $result['mime_type'],
        'mtype' => $type
    ]);

    Auth::logActivity($_SESSION['user_id'], 'upload.file', "Uploaded file: {$result['original_name']}");
    jsonResponse(['success' => true, 'file' => $result], 200);
} catch (Exception $e) {
    jsonResponse(['error' => 'Database error'], 500);
}

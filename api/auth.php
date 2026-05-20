<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        if ($method !== 'POST') { jsonResponse(['error' => 'Method not allowed'], 405); }
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? $_POST['email'] ?? '';
        $password = $data['password'] ?? $_POST['password'] ?? '';
        if (empty($email) || empty($password)) {
            jsonResponse(['error' => 'Email and password required'], 400);
        }
        $result = Auth::login($email, $password);
        if ($result === true) {
            jsonResponse(['success' => true, 'user' => Auth::user()]);
        } elseif ($result === 'unverified') {
            jsonResponse(['error' => 'Email not verified'], 403);
        } else {
            jsonResponse(['error' => 'Invalid credentials'], 401);
        }
        break;

    case 'register':
        if ($method !== 'POST') { jsonResponse(['error' => 'Method not allowed'], 405); }
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            jsonResponse(['error' => 'Username, email, and password required'], 400);
        }
        $result = Auth::register($data);
        if ($result === true) {
            jsonResponse(['success' => true, 'message' => 'Registration successful']);
        } elseif ($result === 'email_exists') {
            jsonResponse(['error' => 'Email already exists'], 409);
        } elseif ($result === 'username_exists') {
            jsonResponse(['error' => 'Username already exists'], 409);
        } else {
            jsonResponse(['error' => 'Registration failed'], 500);
        }
        break;

    case 'me':
        if (!Auth::check()) { jsonResponse(['error' => 'Not authenticated'], 401); }
        jsonResponse(['user' => Auth::user()]);
        break;

    case 'logout':
        Auth::logout();
        jsonResponse(['success' => true, 'message' => 'Logged out']);
        break;

    default:
        jsonResponse(['error' => 'Unknown action'], 400);
}

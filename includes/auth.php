<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

class Auth {
    public static function login($email, $password, $remember = false) {
        $db = db();
        $stmt = $db->prepare("SELECT u.*, r.name as role_name, r.slug as role_slug
                              FROM users u
                              JOIN roles r ON u.role_id = r.id
                              WHERE u.email = :email AND u.status = 'active'
                              LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            self::logActivity(null, 'login.failed', "Failed login attempt for: $email");
            return false;
        }

        if ($user['email_verified_at'] === null) {
            $_SESSION['verify_email'] = $user['email'];
            return 'unverified';
        }

        $sessionId = bin2hex(random_bytes(32));
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['role_slug'] = $user['role_slug'];
        $_SESSION['session_id'] = $sessionId;

        $stmt = $db->prepare("INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, device_name)
                              VALUES (:user_id, :session_id, :ip, :ua, :device)");
        $stmt->execute([
            'user_id' => $user['id'],
            'session_id' => $sessionId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'device' => self::getDeviceName()
        ]);

        $db->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id")
           ->execute(['ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 'id' => $user['id']]);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $db->prepare("UPDATE users SET remember_token = :token WHERE id = :id")
               ->execute(['token' => password_hash($token, PASSWORD_DEFAULT), 'id' => $user['id']]);
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', true, true);
        }

        self::logActivity($user['id'], 'login.success', "User logged in successfully");
        return true;
    }

    public static function register($data) {
        $db = db();
        if (self::emailExists($data['email'])) {
            return 'email_exists';
        }
        if (self::usernameExists($data['username'])) {
            return 'username_exists';
        }

        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $roleId = $data['role_id'] ?? 1;

        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, role_id, bio)
                              VALUES (:username, :email, :password, :full_name, :role_id, :bio)");
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $password,
            'full_name' => $data['full_name'] ?? $data['username'],
            'role_id' => $roleId,
            'bio' => $data['bio'] ?? ''
        ]);

        self::logActivity($db->lastInsertId(), 'register', "New user registered: {$data['email']}");
        return true;
    }

    public static function logout() {
        if (isset($_SESSION['user_id'])) {
            $db = db();
            $stmt = $db->prepare("UPDATE user_sessions SET is_active = 0 WHERE session_id = :sid");
            $stmt->execute(['sid' => $_SESSION['session_id'] ?? '']);
            self::logActivity($_SESSION['user_id'], 'logout', "User logged out");
        }
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        SessionManager::destroy();
    }

    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function user() {
        if (!self::check()) return null;
        $db = db();
        $stmt = $db->prepare("SELECT u.*, r.name as role_name, r.slug as role_slug
                              FROM users u JOIN roles r ON u.role_id = r.id
                              WHERE u.id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }

    public static function requireAuth() {
        if (!self::check()) {
            SessionManager::setFlash('error', 'Please login to continue.');
            header('Location: ' . SITE_URL . '/login.php');
            exit;
        }
    }

    public static function requireRole($roles) {
        self::requireAuth();
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($_SESSION['role_slug'], $roles)) {
            SessionManager::setFlash('error', 'You do not have permission to access this page.');
            header('Location: ' . SITE_URL . '/index.php');
            exit;
        }
    }

    public static function hasRole($roleSlug) {
        return self::check() && $_SESSION['role_slug'] === $roleSlug;
    }

    public static function hasPermission($permissionSlug) {
        if (!self::check()) return false;
        $db = db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM role_permissions rp
                              JOIN permissions p ON rp.permission_id = p.id
                              WHERE rp.role_id = :role_id AND p.slug = :slug");
        $stmt->execute(['role_id' => $_SESSION['role_id'], 'slug' => $permissionSlug]);
        return $stmt->fetchColumn() > 0;
    }

    private static function emailExists($email) {
        $stmt = db()->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ? true : false;
    }

    private static function usernameExists($username) {
        $stmt = db()->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() ? true : false;
    }

    private static function getDeviceName() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (strpos($ua, 'Windows') !== false) return 'Windows';
        if (strpos($ua, 'Mac') !== false) return 'macOS';
        if (strpos($ua, 'Linux') !== false) return 'Linux';
        if (strpos($ua, 'Android') !== false) return 'Android';
        if (strpos($ua, 'iPhone') !== false) return 'iPhone';
        return 'Unknown';
    }

    public static function logActivity($userId, $action, $description = '') {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent)
                                  VALUES (:user_id, :action, :description, :ip, :ua)");
            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'ua' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}

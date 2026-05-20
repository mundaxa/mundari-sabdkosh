<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

function truncate($text, $length = 100, $append = '...') {
    return mb_strlen($text) > $length
        ? mb_substr($text, 0, $length) . $append
        : $text;
}

function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $time);
}

function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

function formatNumber($num) {
    if ($num >= 1000000) return number_format($num / 1000000, 1) . 'M';
    if ($num >= 1000) return number_format($num / 1000, 1) . 'K';
    return number_format($num);
}

function avatar($user, $size = 40) {
    if (!empty($user['avatar'])) {
        return escape($user['avatar']);
    }
    $name = $user['full_name'] ?? $user['username'] ?? 'U';
    $initial = mb_strtoupper(mb_substr($name, 0, 1));
    $colors = ['#4f46e5','#0891b2','#7c3aed','#059669','#d97706','#dc2626','#db2777','#65a30d','#0d9488','#9333ea'];
    $color = $colors[crc32($name) % count($colors)];
    return "https://ui-avatars.com/api/?name=" . urlencode($initial) . "&background=" . urlencode($color) . "&color=fff&size={$size}";
}

function getSetting($key, $default = null) {
    $db = db();
    static $settings = [];
    if (empty($settings)) {
        $result = $db->query("SELECT setting_key, setting_value FROM system_settings");
        while ($row = $result->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings[$key] ?? $default;
}

function notify($userId, $type, $title, $message, $link = '') {
    $db = db();
    $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, link)
                          VALUES (:uid, :type, :title, :msg, :link)");
    $stmt->execute([
        'uid' => $userId,
        'type' => $type,
        'title' => $title,
        'msg' => $message,
        'link' => $link
    ]);
}

function getNotifications($userId, $limit = 10) {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim");
    $stmt->bindValue('uid', $userId, PDO::PARAM_INT);
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function unreadNotificationCount($userId) {
    $db = db();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0");
    $stmt->execute(['uid' => $userId]);
    return $stmt->fetchColumn();
}

function getWords($limit = 10, $offset = 0, $status = 'approved') {
    $db = db();
    $stmt = $db->prepare("SELECT w.*, c.name as category_name, c.color as category_color,
                          u.username as submitter_name
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          LEFT JOIN users u ON w.submitted_by = u.id
                          WHERE w.status = :status
                          ORDER BY w.created_at DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue('status', $status, PDO::PARAM_STR);
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue('off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTrendingWords($limit = 10) {
    $db = db();
    $stmt = $db->prepare("SELECT w.*, c.name as category_name, c.color as category_color
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          WHERE w.status = 'approved'
                          ORDER BY w.views_count DESC LIMIT :lim");
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getWordOfDay() {
    $db = db();
    $stmt = $db->prepare("SELECT w.*, c.name as category_name, c.color as category_color,
                          u.username as submitter_name
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          LEFT JOIN users u ON w.submitted_by = u.id
                          WHERE w.is_word_of_day = 1 AND w.word_of_day_date = CURDATE()
                          AND w.status = 'approved' LIMIT 1");
    $stmt->execute();
    return $stmt->fetch();
}

function searchWords($query, $limit = 20, $offset = 0) {
    $db = db();
    $q = '%' . $query . '%';
    $stmt = $db->prepare("SELECT w.*, c.name as category_name, c.color as category_color
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          WHERE w.status = 'approved'
                          AND (w.word LIKE :q1 OR w.meaning_en LIKE :q2 OR w.meaning_hi LIKE :q3
                               OR w.word_devanagari LIKE :q4 OR w.meaning_mun LIKE :q5)
                          ORDER BY w.views_count DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue('q1', $q, PDO::PARAM_STR);
    $stmt->bindValue('q2', $q, PDO::PARAM_STR);
    $stmt->bindValue('q3', $q, PDO::PARAM_STR);
    $stmt->bindValue('q4', $q, PDO::PARAM_STR);
    $stmt->bindValue('q5', $q, PDO::PARAM_STR);
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue('off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getArticles($limit = 10, $offset = 0, $featured = false) {
    $db = db();
    $where = "WHERE a.status = 'published'";
    if ($featured) $where .= " AND a.is_featured = 1";
    $stmt = $db->prepare("SELECT a.*, u.username as author_name, u.avatar as author_avatar
                          FROM articles a LEFT JOIN users u ON a.author_id = u.id
                          {$where}
                          ORDER BY a.published_at DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue('off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTopContributors($limit = 10) {
    $db = db();
    $stmt = $db->prepare("SELECT u.*, r.name as role_name
                          FROM users u JOIN roles r ON u.role_id = r.id
                          WHERE u.status = 'active'
                          ORDER BY u.contributions DESC LIMIT :lim");
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategories() {
    $db = db();
    $stmt = $db->prepare("SELECT c.*, (SELECT COUNT(*) FROM words WHERE category_id = c.id AND status = 'approved') as word_count
                          FROM categories c WHERE c.status = 'active' ORDER BY c.sort_order, c.name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getRecentActivity($limit = 10) {
    $db = db();
    $stmt = $db->prepare("SELECT al.*, u.username, u.avatar
                          FROM activity_logs al
                          LEFT JOIN users u ON al.user_id = u.id
                          ORDER BY al.created_at DESC LIMIT :lim");
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function uploadFile($file, $type = 'image') {
    $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
        'video' => ['mp4', 'webm', 'ogg'],
        'document' => ['pdf', 'doc', 'docx', 'txt']
    ];

    if (!isset($allowedTypes[$type])) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes[$type])) return false;

    $maxSize = MAX_UPLOAD_SIZE;
    if ($file['size'] > $maxSize) return false;

    $dir = UPLOAD_PATH . $type . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'filepath' => $filepath,
            'filesize' => $file['size'],
            'mime_type' => $file['type'],
            'media_type' => $type,
            'filetype' => $ext
        ];
    }
    return false;
}

function generateCsrfToken() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="_csrf_token" value="' . generateCsrfToken() . '">';
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function paginate($total, $currentPage, $itemsPerPage) {
    $totalPages = ceil($total / $itemsPerPage);
    return [
        'total' => $total,
        'current_page' => $currentPage,
        'per_page' => $itemsPerPage,
        'total_pages' => $totalPages,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage - 1,
        'next_page' => $currentPage + 1
    ];
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^\w\.\-]/', '_', $filename);
    return preg_replace('/_+/', '_', $filename);
}

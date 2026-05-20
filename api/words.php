<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    $db = db();

    switch ($action) {
        case 'word_of_day':
            $stmt = $db->prepare("SELECT w.*, c.name as category_name
                                  FROM words w LEFT JOIN categories c ON w.category_id = c.id
                                  WHERE w.is_word_of_day = 1 AND w.word_of_day_date = CURDATE()
                                  AND w.status = 'approved' LIMIT 1");
            $stmt->execute();
            echo json_encode($stmt->fetch() ?: null);
            break;

        case 'trending':
            $limit = min(intval($_GET['limit'] ?? 10), 50);
            $stmt = $db->prepare("SELECT w.id, w.word, w.word_devanagari, w.meaning_en,
                                  w.views_count, c.name as category_name, c.color as category_color
                                  FROM words w LEFT JOIN categories c ON w.category_id = c.id
                                  WHERE w.status = 'approved'
                                  ORDER BY w.views_count DESC LIMIT :lim");
            $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
            break;

        case 'recent':
            $limit = min(intval($_GET['limit'] ?? 10), 50);
            $stmt = $db->prepare("SELECT w.id, w.word, w.word_devanagari, w.meaning_en,
                                  w.created_at, c.name as category_name
                                  FROM words w LEFT JOIN categories c ON w.category_id = c.id
                                  WHERE w.status = 'approved'
                                  ORDER BY w.created_at DESC LIMIT :lim");
            $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
            break;

        case 'detail':
            $id = intval($_GET['id'] ?? 0);
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'Missing id']); exit; }
            $stmt = $db->prepare("SELECT w.*, c.name as category_name, u.username as submitter_name
                                  FROM words w
                                  LEFT JOIN categories c ON w.category_id = c.id
                                  LEFT JOIN users u ON w.submitted_by = u.id
                                  WHERE w.id = :id");
            $stmt->execute(['id' => $id]);
            $word = $stmt->fetch();
            if ($word) {
                $db->prepare("UPDATE words SET views_count = views_count + 1 WHERE id = :id")->execute(['id' => $id]);
            }
            echo json_encode($word ?: null);
            break;

        case 'search':
            $query = trim($_GET['q'] ?? '');
            if (strlen($query) < 2) { echo json_encode([]); exit; }
            $q = '%' . $query . '%';
            $stmt = $db->prepare("SELECT w.id, w.word, w.word_devanagari, w.meaning_en,
                                  c.name as category_name, c.color as category_color
                                  FROM words w LEFT JOIN categories c ON w.category_id = c.id
                                  WHERE w.status = 'approved'
                                  AND (w.word LIKE :q1 OR w.meaning_en LIKE :q2
                                       OR w.word_devanagari LIKE :q3)
                                  ORDER BY w.views_count DESC LIMIT 20");
            $stmt->bindValue('q1', $q, PDO::PARAM_STR);
            $stmt->bindValue('q2', $q, PDO::PARAM_STR);
            $stmt->bindValue('q3', $q, PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
            break;

        default:
            $limit = min(intval($_GET['limit'] ?? 20), 100);
            $offset = max(intval($_GET['offset'] ?? 0), 0);
            $stmt = $db->prepare("SELECT w.id, w.word, w.word_devanagari, w.meaning_en,
                                  w.pronunciation, w.views_count, w.created_at,
                                  c.name as category_name, c.color as category_color
                                  FROM words w LEFT JOIN categories c ON w.category_id = c.id
                                  WHERE w.status = 'approved'
                                  ORDER BY w.created_at DESC LIMIT :lim OFFSET :off");
            $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue('off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}

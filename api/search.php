<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$query = trim($_GET['q'] ?? '');
$format = $_GET['format'] ?? 'json';

if (empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = db();
    $q = '%' . $query . '%';
    $stmt = $db->prepare("SELECT w.id, w.word, w.word_devanagari, w.meaning_en, w.meaning_hi,
                          c.name as category_name, c.color as category_color,
                          w.pronunciation
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          WHERE w.status = 'approved'
                          AND (w.word LIKE :q1 OR w.meaning_en LIKE :q2 OR w.meaning_hi LIKE :q3
                               OR w.word_devanagari LIKE :q4)
                          ORDER BY w.views_count DESC
                          LIMIT 10");
    $stmt->bindValue('q1', $q, PDO::PARAM_STR);
    $stmt->bindValue('q2', $q, PDO::PARAM_STR);
    $stmt->bindValue('q3', $q, PDO::PARAM_STR);
    $stmt->bindValue('q4', $q, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();

    if ($format === 'json') {
        echo json_encode($results);
    } else {
        echo json_encode($results);
    }

    if (!empty($results)) {
        $ids = array_column($results, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("UPDATE words SET search_count = search_count + 1 WHERE id IN ($placeholders)")
           ->execute($ids);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}

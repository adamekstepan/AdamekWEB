<?php
require_once __DIR__ . '/../backend/config/Database.php';

try {
    $db = Database::connect();
    $stmt = $db->query('SELECT 1');
    $dbStatus = $stmt ? 'connected' : 'error';
} catch (Exception $e) {
    $dbStatus = 'error';
}

echo json_encode([
    'status' => 'ok',
    'timestamp' => time(),
    'database' => $dbStatus
]);

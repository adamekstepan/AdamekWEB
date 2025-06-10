<?php
require_once __DIR__ . '/../backend/Middleware/JwtAuthMiddleware.php';
require_once __DIR__ . '/../backend/config/Database.php';

header('Content-Type: application/json');

try {
    $user = JwtAuthMiddleware::verifyToken();

    $pdo = Database::connect();
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ],
        'products' => $products
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Chyba serveru']);
}

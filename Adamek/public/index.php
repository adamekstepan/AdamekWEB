<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/controller/ProductController.php';
require_once __DIR__ . '/../backend/middleware/AuthMiddleware.php';

header('Content-Type: application/json');

$route = $_GET['route'] ?? '';

switch ($route) {
    case 'products':
        $method = $_SERVER['REQUEST_METHOD'];
        $controller = new ProductController();

        if ($method === 'GET') {
            $controller->getAllProducts();
        } elseif ($method === 'POST') {
             echo "post přijat";
            $data = json_decode(file_get_contents("php://input"), true);
            $controller->createProduct($data);
        } elseif ($method === 'DELETE' && isset($_GET['id'])) {
            $controller->deleteProduct((int) $_GET['id']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Nepodporovaná HTTP metoda']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Neznámý endpoint']);
        break;
}
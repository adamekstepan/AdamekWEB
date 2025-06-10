<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../backend/config/Database.php';

const JWT_SECRET = 'tajny_klic_123';

header('Content-Type: application/json');

function create_jwt(array $payload, string $secret): string {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $body = base64_encode(json_encode($payload));
    $signature = base64_encode(hash_hmac('sha256', "$header.$body", $secret, true));
    return "$header.$body.$signature";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    try {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE name = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'id' => $user['id'],
                'username' => $user['name'],
                'role' => $user['role'] ?? 'admin',
                'exp' => time() + 3600
            ];
            $token = create_jwt($payload, JWT_SECRET);

            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Neplatné přihlašovací údaje']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Chyba serveru',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metoda není povolena']);
}

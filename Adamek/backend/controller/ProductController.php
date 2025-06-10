<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../repository/ProductRepository.php';
require_once __DIR__ . '/../service/ProductService.php';
require_once __DIR__ . '/../dto/ProductDTO.php';
require_once __DIR__ . '/../utils/ProductValidator.php';
require_once __DIR__ . '/../utils/Logger.php';

class ProductController {
    private ProductService $service;

    public function __construct() {
        $pdo = Database::connect();
        $repo = new ProductRepository($pdo);
        $this->service = new ProductService($repo);
    }

    public function handleRequest(): array {
    $messages = [];

    if (isset($_POST['add_product'])) {
        Logger::info("DEBUG: Detekováno přidání produktu");

        $dto = new ProductDTO([
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'image' => $_FILES['image']['name'],
            'restaurant_id' => $_POST['restaurant_id'] ?? 0
        ]);

        $errors = ProductValidator::validate($dto);
        Logger::info("DEBUG: Výsledek validace: " . json_encode($errors));

        if (!empty($errors)) {
            $messages = array_merge($messages, $errors);
        } else {
            $this->service->createProduct($dto);
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploaded_img/' . $dto->image);
            $messages[] = "Produkt byl úspěšně přidán";
        }
    }

    if (isset($_GET['delete'])) {
        $this->service->deleteProduct((int)$_GET['delete']);
        header("Location: admin_products.php");
        exit;
    }

    return $messages;
}

    public function getAllProducts(): void {
        Logger::info("DEBUG: Volání getAllProducts()");
        echo json_encode($this->service->getAllProducts());
    }

    public function createProduct(array $data): void {
        Logger::info("DEBUG: API createProduct voláno");
        $dto = new ProductDTO($data);
        $result = $this->service->createProduct($dto);
        echo json_encode($result);
    }

    public function deleteProduct(int $id): void {
        Logger::info("DEBUG: API deleteProduct voláno pro ID: $id");
        $this->service->deleteProduct($id);
        echo json_encode(['message' => 'Produkt smazán']);
    }
}

<?php

class ProductRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(string $name, float $price, string $image, int $restaurant_id): void {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, price, image, restaurant_id, approved) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$name, $price, $image, $restaurant_id]);
    }

    public function deleteById(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }
}

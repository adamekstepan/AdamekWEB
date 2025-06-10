<?php

class ProductDTO {
    public string $name;
    public float $price;
    public string $image;
    public int $restaurant_id;

    public function __construct(array $data) {
        $this->name = trim($data['name'] ?? '');
        $this->price = floatval($data['price'] ?? 0);
        $this->image = trim($data['image'] ?? '');
        $this->restaurant_id = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : 0;
    }
}

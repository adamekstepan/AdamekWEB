<?php

require_once __DIR__ . '/../utils/ProductValidator.php';
require_once __DIR__ . '/../utils/Logger.php';

class ProductService {
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllProducts(): array {
        Logger::info("Načtení všech produktů");
        return $this->repository->findAll();
    }

    public function createProduct(ProductDTO $dto): array {
        Logger::info("DEBUG: createProduct spuštěno");
        Logger::info("DEBUG: Final restaurant_id před uložením: " . $dto->restaurant_id);

        Logger::info("DEBUG: DTO data: " . json_encode([
        
            'name' => $dto->name,
            'price' => $dto->price,
            'image' => $dto->image,
            'restaurant_id' => $dto->restaurant_id
        ]));

        $errors = ProductValidator::validate($dto);
        Logger::info("DEBUG: Výsledek validace v service: " . json_encode($errors));

        if (!empty($errors)) {
            Logger::warning("Neplatná data při vytváření produktu: " . json_encode($errors));
            return ['errors' => $errors];
        }

        $this->repository->insert($dto->name, $dto->price, $dto->image, $dto->restaurant_id);
        Logger::info("Vytvořen produkt: " . $dto->name);
        return ['message' => 'Produkt byl úspěšně vytvořen'];
    }

    public function deleteProduct(int $id): void {
        Logger::info("DEBUG: deleteProduct spuštěno s ID: " . $id);
        $this->repository->deleteById($id);
        Logger::info("Smazán produkt ID: " . $id);
    }
}

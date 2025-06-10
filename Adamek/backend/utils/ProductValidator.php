<?php

class ProductValidator {
    public static function validate(ProductDTO $dto): array {
        $errors = [];

        if (strlen($dto->name) < 3) {
            $errors[] = "Název musí mít alespoň 3 znaky.";
        }

        if ($dto->price <= 0) {
            $errors[] = "Cena musí být větší než 0.";
        }

        if (!preg_match('/\.(jpg|jpeg|png)$/i', $dto->image)) {
            $errors[] = "Obrázek musí být JPG nebo PNG.";
        }

        if ($dto->restaurant_id <= 0) {
    $errors[] = "Musíš vybrat platnou restauraci.";
}

        return $errors;
    }
}

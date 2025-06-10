<?php

class JwtAuthMiddleware {
    const SECRET = 'tajny_klic_123';

    public static function verifyToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            exit('Chybějící nebo neplatný Authorization header');
        }

        $token = substr($authHeader, 7);
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            http_response_code(401);
            exit('Neplatný formát tokenu');
        }

        [$headerB64, $bodyB64, $signatureB64] = $parts;

        $expectedSig = base64_encode(
            hash_hmac('sha256', "$headerB64.$bodyB64", self::SECRET, true)
        );

        if (!hash_equals($expectedSig, $signatureB64)) {
            http_response_code(401);
            exit('Neplatný podpis tokenu');
        }

        $payload = json_decode(base64_decode($bodyB64), true);

        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            http_response_code(401);
            exit('Token expiroval nebo je poškozen');
        }

        return $payload;
    }
}

<?php

namespace App\Domain\PPDB\Services;

class StatusTokenService
{
    public function generateToken(): string
    {
        // token mentah untuk URL
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }   
}
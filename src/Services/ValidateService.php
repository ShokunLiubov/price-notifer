<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class ValidateService
{
    public static function isValidEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @throws Exception
     */
    public function isValidUrl($url): static
    {
        if (empty($url)) {
            throw new Exception('URL is required');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL) !== false) {
            throw new Exception('Invalid URL');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function validateEmail(string $email): static
    {
        if (empty($email)) {
            throw new Exception('Email is required');
        }

        if (!self::isValidEmail($email)) {
            throw new Exception('Invalid email');
        }

        return $this;
    }
}

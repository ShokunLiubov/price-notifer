<?php

declare(strict_types=1);

namespace App\Dto;

class SubscriptionDTO extends BaseDTO
{
    public function __construct(
        public string $email = '',
        public int $olxAdvertId = 0,
        public string $link = ''
    ) {
    }
}
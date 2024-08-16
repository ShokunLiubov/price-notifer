<?php

declare(strict_types=1);

namespace App\Dto;

class AdvertDTO extends BaseDTO
{
    public function __construct(
        public int $olxAdvertId = 0,
        public string $link = '',
        public string $title = '',
        public string $linkImage = '',
        public int $price = 0,
        public string $currency = '',
    ) {
    }
}
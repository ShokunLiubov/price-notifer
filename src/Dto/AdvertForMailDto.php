<?php

declare(strict_types=1);

namespace App\Dto;

class AdvertForMailDto extends BaseDTO
{
    public function __construct(
        public int $id = 0,
        public int $lastPrice = 0,
        public int $currentPrice = 0,
        public string $link = '',
        public string $currency = '',
        public string $title = '',
        public string $linkImage = '',
    ) {
    }
}
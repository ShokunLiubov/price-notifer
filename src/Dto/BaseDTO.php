<?php

declare(strict_types=1);

namespace App\Dto;

abstract class BaseDTO
{
    public static function snakeToCamelCase($string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    public static function fromArray(array $data): static
    {
        $dto = new static();

        foreach ($data as $key => $value) {
            $property = self::snakeToCamelCase($key);
            if (property_exists($dto, $property)) {
                $dto->$property = $value;
            }
        }

        return $dto;
    }
}

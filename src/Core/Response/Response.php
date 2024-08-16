<?php

declare(strict_types=1);

namespace App\Core\Response;

class Response
{
    // not fully implemented
    public function view(string $view, array $data = []): static
    {
        echo $view;

        return $this;
    }
}
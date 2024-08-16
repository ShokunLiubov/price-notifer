<?php

declare(strict_types=1);

namespace App\Core\Response;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected mixed $content;

    public function setHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;

        return $this;
    }

    // not fully implemented
    public function json(array $data): static
    {
        $this->content = json_encode($data);
        $this->setHeader('Content-Type', 'application/json');
        $this->send();

        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if (!empty($this->content)) {
            header('Content-Length: ' . strlen($this->content));
            file_put_contents('php://output', $this->content);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Commands;

use Exception;

abstract class Command
{
    private const LOG_PATH = '/var/log/cron.log';

    abstract public function handle(): void;

    protected function logError(Exception $e): void
    {
        $message = date('Y-m-d H:i:s') . "Error: {$e->getMessage()}";

        file_put_contents(self::LOG_PATH, $message, FILE_APPEND);
    }
}

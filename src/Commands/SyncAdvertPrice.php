<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\OLXApiService;
use Exception;

class SyncAdvertPrice extends Command
{

    public function handle(): void
    {
        try {
            (new OLXApiService())->syncPrices();
        } catch (Exception $e) {
            $this->logError($e);
        }
    }
}
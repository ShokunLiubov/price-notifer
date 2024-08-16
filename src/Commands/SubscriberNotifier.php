<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\OLXSubscriberService;
use Exception;

class SubscriberNotifier extends Command
{
    public function handle(): void
    {
        try {
            (new OLXSubscriberService())->notifySubscribers();
        } catch (Exception $e) {
            $this->logError($e);
        }
    }
}

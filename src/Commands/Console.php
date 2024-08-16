<?php

declare(strict_types=1);

use App\Commands\SubscriberNotifier;
use App\Commands\SyncAdvertPrice;

require __DIR__ . '/../../init.php';

$args = $_SERVER['argv'];

match ($args[1]) {
    'SyncAdvertPrice' => (new SyncAdvertPrice())->handle(),
    'SubscriberNotifier' => (new SubscriberNotifier())->handle(),
    default => throw new Exception('Command not found'),
};

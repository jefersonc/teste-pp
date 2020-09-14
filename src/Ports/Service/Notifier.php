<?php

namespace Jefersonc\TestePP\Ports\Service;

use Jefersonc\TestePP\Domain\Transaction\Transaction;

interface Notifier
{
    public function notify(Transaction $transaction): bool;
}

<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction;

use Iterator;
use Jefersonc\TestePP\Infra\Common\IteratorCapabilities;

final class TransactionCollection implements Iterator
{
    use IteratorCapabilities;

    public function add(Transaction $transfer): void {
        array_push($this->list, $transfer);
    }
}

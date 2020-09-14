<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Ports\Repository;

use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Domain\Transaction\TransactionCollection;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;

interface TransactionRepository
{
    public function push(Transaction $transaction): void;

    public function getTransactionsByCustomerId(Uuid $customerId): TransactionCollection;
}

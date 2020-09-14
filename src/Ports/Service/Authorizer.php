<?php

namespace Jefersonc\TestePP\Ports\Service;

use Jefersonc\TestePP\Domain\Transaction\Transaction;

interface Authorizer
{
    public function authorize(Transaction $transaction): bool;
}

<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction\Command;

use Jefersonc\TestePP\Domain\Customer\Customer;

class CreateTransferCommand
{
    private Customer $payee;
    private Customer $payer;
    private float $value;

    /**
     * CreateTransferCommand constructor.
     * @param Customer $payee
     * @param Customer $payer
     * @param float $value
     */
    public function __construct(Customer $payer, Customer $payee, float $value)
    {
        $this->payee = $payee;
        $this->payer = $payer;
        $this->value = $value;
    }

    /**
     * @return Customer
     */
    public function getPayee(): Customer
    {
        return $this->payee;
    }

    /**
     * @return Customer
     */
    public function getPayer(): Customer
    {
        return $this->payer;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}

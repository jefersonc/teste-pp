<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction;

use DateTime;
use Jefersonc\TestePP\Domain\Customer\Customer;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;

final class Transaction
{
    /**
     * @var Uuid
     */
    private Uuid $id;

    /**
     * @var Uuid
     */
    private Uuid $payer;

    /**
     * @var Uuid
     */
    private Uuid $payee;

    /**
     * @var float
     */
    private float $value;

    /**
     * @var DateTime
     */
    private DateTime $date;

    /**
     * Transaction constructor.
     * @param Uuid $id
     * @param Uuid $payer
     * @param Uuid $payee
     * @param float $value
     * @param DateTime $date
     */
    public function __construct(
        Uuid $id,
        Uuid $payer,
        Uuid $payee,
        float $value,
        DateTime $date
    )
    {
        $this->id = $id;
        $this->payer = $payer;
        $this->payee = $payee;
        $this->value = $value;
        $this->date = $date;
    }

    public static function generate(Customer $payer, Customer $payee, float $value): Transaction
    {
        return new self(Uuid::generate(), $payer->getId(), $payee->getId(), $value, new DateTime());
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Uuid
     */
    public function getPayer(): Uuid
    {
        return $this->payer;
    }

    /**
     * @return Uuid
     */
    public function getPayee(): Uuid
    {
        return $this->payee;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }
}

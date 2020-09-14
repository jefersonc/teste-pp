<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Customer;

use Jefersonc\TestePP\Domain\Auth\User\User;
use Jefersonc\TestePP\Domain\Transaction\TransactionCollection;
use Jefersonc\TestePP\Infra\ValueObject\Document;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;

class Customer
{
    private Uuid $id;

    private int $externalCode;

    private string $name;

    private Document $document;

    private User $user;

    private TransactionCollection $transactions;

    public function __construct(
        Uuid $id,
        int $externalCode,
        string $name,
        User $user,
        Document $document,
        TransactionCollection $transactions
    )
    {
        $this->id = $id;
        $this->externalCode = $externalCode;
        $this->name = $name;
        $this->user = $user;
        $this->document = $document;
        $this->transactions = $transactions;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getExternalCode(): int
    {
        return $this->externalCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return TransactionCollection
     */
    public function getTransactions(): TransactionCollection
    {
        return $this->transactions;
    }

    /**
     * @return bool
     */
    public function canSendFunds(): bool
    {
        if ($this->getDocument()->getType() === Document::CPF) {
            return true;
        }

        return false;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        $balance = 0;

        foreach ($this->transactions as $transaction) {
            if($transaction->getPayee()->getValue() === $this->getId()->getValue()) {
                $balance += $transaction->getValue();
                continue;
            }

            $balance -= $transaction->getValue();
        }

        return $balance;
    }
}

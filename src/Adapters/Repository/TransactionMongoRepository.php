<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\Repository;

use DateTime;
use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Domain\Transaction\TransactionCollection;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;
use Jefersonc\TestePP\Ports\Repository\TransactionRepository;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Log\LoggerInterface;

final class TransactionMongoRepository implements TransactionRepository
{
    private Collection $collection;
    private LoggerInterface $logger;

    public function __construct(Database $database, LoggerInterface $logger) {
        $this->collection = $database->selectCollection('transaction');
        $this->logger = $logger;
    }

    public function push(Transaction $transaction): void {
        $raw = $this->dehydrate($transaction);

        $this->collection->insertOne($raw);
    }

    public function getTransactionsByCustomerId(Uuid $customerId): TransactionCollection
    {
        $raw = $this->collection->find([
            '$or' => [
                [
                    'payee' => $customerId->getValue(),
                ],
                [
                    'payer' => $customerId->getValue(),
                ]
            ]
        ]);

        return $this->hydrateCollection($raw);
    }

    private function dehydrate(Transaction $transaction)
    {
        return [
            'id' => $transaction->getId()->getValue(),
            'payee' => $transaction->getPayee()->getValue(),
            'payer' => $transaction->getPayer()->getValue(),
            'value' => $transaction->getValue(),
            'date' => $transaction->getDate()->format('Y-m-d H:i:s.u')
        ];
    }

    private function hydrateCollection(\MongoDB\Driver\Cursor $raw)
    {
        $collection = new TransactionCollection();

        $transactions = $raw->toArray();

        foreach ($transactions as $transaction) {
            $collection->add($this->hydrate($transaction));
        }

        return $collection;
    }

    private function hydrate(object $raw)
    {
        return new Transaction(
            new Uuid($raw->id),
            new Uuid($raw->payer),
            new Uuid($raw->payee),
            $raw->value,
            DateTime::createFromFormat('Y-m-d H:i:s.u', $raw->date)
        );
    }
}

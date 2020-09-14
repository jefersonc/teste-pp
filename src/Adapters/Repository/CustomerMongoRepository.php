<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\Repository;

use Jefersonc\TestePP\Domain\Auth\User\User;
use Jefersonc\TestePP\Domain\Customer\Customer;
use Jefersonc\TestePP\Infra\ValueObject\Document;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;
use Jefersonc\TestePP\Ports\Repository\CustomerRepository;
use Jefersonc\TestePP\Ports\Repository\TransactionRepository;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Log\LoggerInterface;

final class CustomerMongoRepository implements CustomerRepository
{
    private Collection $collection;
    private LoggerInterface $logger;
    private TransactionRepository $transactionRepository;

    public function __construct(Database $database, LoggerInterface $logger, TransactionRepository $transactionRepository) {
        $this->collection = $database->selectCollection('customer');
        $this->logger = $logger;
        $this->transactionRepository = $transactionRepository;
    }

    public function push(Customer $customer): void {
        $raw = $this->dehydrate($customer);

        $this->collection->insertOne($raw);
    }
    private function find(array $filter): ?Customer {
        $raw = $this->collection->findOne($filter);

        if (!$raw) {
            return null;
        }

        return $this->hydrate($raw);
    }

    public function findByExternalCode(int $externalCode): ?Customer
    {
        return $this->find([
            "external_code" => $externalCode
        ]);
    }

    private function dehydrate(Customer $customer)
    {
        return [
            'id' => $customer->getId()->getValue(),
            'external_code' => $customer->getExternalCode(),
            'name' => $customer->getName(),
            'user' => [
                'email' => $customer->getUser()->getEmail(),
                'password' => $customer->getUser()->getPassword()
            ],
            'document' => [
                'type' => $customer->getDocument()->getType(),
                'number' => $customer->getDocument()->getNumber()
            ]
        ];
    }

    private function hydrate(object $raw): Customer
    {
        $customerId = new Uuid($raw->id);

        $user = new User(
            $raw->user->email,
            $raw->user->password
        );

        $document = new Document(
            $raw->document->type,
            $raw->document->number
        );

        $transactions = $this
            ->transactionRepository
            ->getTransactionsByCustomerId($customerId);

        return new Customer(
            $customerId,
            (int) $raw->external_code,
            $raw->name,
            $user,
            $document,
            $transactions
        );
    }
}

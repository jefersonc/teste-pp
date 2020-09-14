<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction\Action;

use Jefersonc\TestePP\Domain\Customer\Customer;
use Jefersonc\TestePP\Domain\Transaction\Exception\AuthorizationFailed;
use Jefersonc\TestePP\Domain\Transaction\Exception\CustomerNotAbleToSendFunds;
use Jefersonc\TestePP\Domain\Transaction\Exception\FundsLocked;
use Jefersonc\TestePP\Domain\Transaction\Exception\InsufficientFunds;
use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Ports\Infra\Lock;
use Jefersonc\TestePP\Ports\Repository\TransactionRepository;
use Jefersonc\TestePP\Ports\Service\Authorizer;
use Jefersonc\TestePP\Ports\Service\Notifier;
use Psr\Log\LoggerInterface;

class Transfer
{
    private Authorizer $authorizerService;

    private Notifier $notifierService;

    private TransactionRepository $transactionRepository;

    private LoggerInterface $logger;

    public function __construct(
        Authorizer $authorizerService,
        Notifier $notificationService,
        TransactionRepository $transactionRepository,
        LoggerInterface $logger
    ) {
        $this->authorizerService = $authorizerService;
        $this->notifierService = $notificationService;
        $this->transactionRepository = $transactionRepository;
        $this->logger = $logger;
    }

    public function __invoke(Customer $payer, Customer $payee, float $value): Transaction
    {
        $transaction = Transaction::generate($payer, $payee, $value);

        if (! $payer->canSendFunds()) {
            throw new CustomerNotAbleToSendFunds;
        }

        if ($payer->getBalance() < $value) {
            throw new InsufficientFunds;
        }

        if (!$this->authorizerService->authorize($transaction)) {
            throw new AuthorizationFailed;
        }

        $this->transactionRepository->push($transaction);

        $this->logger->info("Transaction completed", [
            'transaction' => $transaction->getId()->getValue()
        ]);

        // todo: enfileirar isso aqui
        $this->notifierService->notify($transaction);

        return $transaction;
    }
}

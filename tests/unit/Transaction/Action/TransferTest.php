<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Tests\Unit\Transaction\Action;

use Codeception\TestCase\Test;
use Jefersonc\TestePP\Domain\Auth\User\User;
use Jefersonc\TestePP\Domain\Customer\Customer;
use Jefersonc\TestePP\Domain\Transaction\Action\Transfer;
use Jefersonc\TestePP\Domain\Transaction\Exception\AuthorizationFailed;
use Jefersonc\TestePP\Domain\Transaction\Exception\CustomerNotAbleToSendFunds;
use Jefersonc\TestePP\Domain\Transaction\Exception\InsufficientFunds;
use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Domain\Transaction\TransactionCollection;
use Jefersonc\TestePP\Infra\ValueObject\Document;
use Jefersonc\TestePP\Infra\ValueObject\Uuid;
use Jefersonc\TestePP\Ports\Repository\TransactionRepository;
use Jefersonc\TestePP\Ports\Service\Authorizer;
use Jefersonc\TestePP\Ports\Service\Notifier;
use Jefersonc\TestePP\Tests\Mock\Logger;

class TransferTest extends Test
{
    public function testSuccessfullyTransfer() {
        $payeeId = Uuid::generate();
        $payerId = Uuid::generate();

        $payee = new Customer(
            $payeeId,
            4,
            "Teste 1",
            new User("teste1@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection()
        );

        $payer = new Customer(
            $payerId,
            15,
            "Teste 2",
            new User("teste2@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection(
                [
                    new Transaction(Uuid::generate(), $payeeId, $payerId, 1000.00, new \DateTime()),
                ]
            )
        );

        $authorizerServiceStub = $this
            ->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authorizerServiceStub->method('authorize')->willReturn(true);

        $notifierServiceStub = $this
            ->getMockBuilder(Notifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notifierServiceStub->method('notify')->willReturn(true);

        $transactionRepositoryStub = $this
            ->getMockBuilder(TransactionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transferAction = new Transfer(
            $authorizerServiceStub,
            $notifierServiceStub,
            $transactionRepositoryStub,
            new Logger()
        );

        $transaction = $transferAction($payer, $payee, 1.50);

        static::assertEquals($payeeId->getValue(), $transaction->getPayee()->getValue());
        static::assertEquals($payerId->getValue(), $transaction->getPayer()->getValue());
        static::assertEquals(1.50, $transaction->getValue());
    }

    public function testCustomerNotAbleToSendFundsException() {
        $payeeId = Uuid::generate();
        $payerId = Uuid::generate();

        $payee = new Customer(
            $payeeId,
            4,
            "Teste 1",
            new User("teste1@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection()
        );

        $payer = new Customer(
            $payerId,
            15,
            "Teste 2",
            new User("teste2@teste.com", "teste"),
            new Document(Document::CNPJ, "00.000.000/0000-00"),
            new TransactionCollection(
                [
                    new Transaction(Uuid::generate(), $payeeId, $payerId, 1000.00, new \DateTime()),
                ]
            )
        );

        $authorizerServiceStub = $this
            ->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authorizerServiceStub->method('authorize')->willReturn(true);

        $notifierServiceStub = $this
            ->getMockBuilder(Notifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notifierServiceStub->method('notify')->willReturn(true);

        $transactionRepositoryStub = $this
            ->getMockBuilder(TransactionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transferAction = new Transfer(
            $authorizerServiceStub,
            $notifierServiceStub,
            $transactionRepositoryStub,
            new Logger()
        );

        static::expectException(CustomerNotAbleToSendFunds::class);

        $transferAction($payer, $payee, 1.50);
    }

    public function testInsuficientFundsException() {
        $payeeId = Uuid::generate();
        $payerId = Uuid::generate();

        $payee = new Customer(
            $payeeId,
            4,
            "Teste 1",
            new User("teste1@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection()
        );

        $payer = new Customer(
            $payerId,
            15,
            "Teste 2",
            new User("teste2@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection()
        );

        $authorizerServiceStub = $this
            ->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authorizerServiceStub->method('authorize')->willReturn(true);

        $notifierServiceStub = $this
            ->getMockBuilder(Notifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notifierServiceStub->method('notify')->willReturn(true);

        $transactionRepositoryStub = $this
            ->getMockBuilder(TransactionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transferAction = new Transfer(
            $authorizerServiceStub,
            $notifierServiceStub,
            $transactionRepositoryStub,
            new Logger()
        );

        static::expectException(InsufficientFunds::class);

        $transferAction($payer, $payee, 1.50);
    }

    public function testAuthorizationFailedException() {
        $payeeId = Uuid::generate();
        $payerId = Uuid::generate();

        $payee = new Customer(
            $payeeId,
            4,
            "Teste 1",
            new User("teste1@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection()
        );

        $payer = new Customer(
            $payerId,
            15,
            "Teste 2",
            new User("teste2@teste.com", "teste"),
            new Document(Document::CPF, "000.000.00-00"),
            new TransactionCollection(
                [
                    new Transaction(Uuid::generate(), $payeeId, $payerId, 1000.00, new \DateTime()),
                ]
            )
        );

        $authorizerServiceStub = $this
            ->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authorizerServiceStub->method('authorize')->willReturn(false);

        $notifierServiceStub = $this
            ->getMockBuilder(Notifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notifierServiceStub->method('notify')->willReturn(true);

        $transactionRepositoryStub = $this
            ->getMockBuilder(TransactionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transferAction = new Transfer(
            $authorizerServiceStub,
            $notifierServiceStub,
            $transactionRepositoryStub,
            new Logger()
        );

        static::expectException(AuthorizationFailed::class);

        $transferAction($payer, $payee, 1.50);
    }
}

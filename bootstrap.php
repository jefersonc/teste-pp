<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$builder = new DI\ContainerBuilder();
$builder->addDefinitions(require_once 'src/dependencies.php');
$container = $builder->build();

$app = AppFactory::create(null, $container);
$app->addBodyParsingMiddleware();

$app->post('/transaction', function (Request $request, Response $response) use ($container) {
    $customerRepository = $container->get(\Jefersonc\TestePP\Adapters\Repository\CustomerMongoRepository::class);
    $commandBus = $container->get(\Jefersonc\TestePP\Infra\CommandBus\CommandBus::class);

    $payload = $request->getParsedBody();

    $payee = $customerRepository->findByExternalCode($payload['payee']);
    $payer = $customerRepository->findByExternalCode($payload['payer']);

    if (!$payee) {
        throw new \Jefersonc\TestePP\Infra\Exception\EntityNotFoundException("Payee not found");
    }

    if (!$payer) {
        throw new \Jefersonc\TestePP\Infra\Exception\EntityNotFoundException("Payer not found");
    }


    $command = new \Jefersonc\TestePP\Domain\Transaction\Command\CreateTransferCommand(
        $payer,
        $payee,
        $payload['value']
    );

    $commandBus->dispatch($command);

    return $response->withStatus(204);
})->add(
        new \Jefersonc\TestePP\Infra\Middleware\PayloadValidationMiddleware(
            \Jefersonc\TestePP\Infra\Http\Validation\SolicitTransfer::scheme
        )
    )
    ->add($container->get(\Jefersonc\TestePP\Infra\Middleware\LockBalanceMiddleware::class))
    ->add($container->get(\Jefersonc\TestePP\Infra\Middleware\ErrorHandlerMiddleware::class));

$app->run();

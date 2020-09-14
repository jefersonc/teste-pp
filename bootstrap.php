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
    $transfer = $container->get(\Jefersonc\TestePP\Domain\Transaction\Action\Transfer::class);
    $customerRepository = $container->get(\Jefersonc\TestePP\Adapters\Repository\CustomerMongoRepository::class);

    $payload = $request->getParsedBody();

    $payee = $customerRepository->findByExternalCode($payload['payee']);
    $payer = $customerRepository->findByExternalCode($payload['payer']);

    $transfer($payer, $payee, $payload['value']);

    return $response;
})->add(
    new \Jefersonc\TestePP\Infra\Middleware\PayloadValidationMiddleware(
        \Jefersonc\TestePP\Infra\Http\Validation\SolicitTransfer::scheme
    )
)
    ->add($container->get(\Jefersonc\TestePP\Infra\Middleware\LockBalanceMiddleware::class))
    ->add($container->get(\Jefersonc\TestePP\Infra\Middleware\ErrorHandlerMiddleware::class));

$app->run();

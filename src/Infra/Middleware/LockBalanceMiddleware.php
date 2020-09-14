<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Middleware;

use Jefersonc\TestePP\Domain\Transaction\Exception\FundsLocked;
use Jefersonc\TestePP\Ports\Infra\Lock;
use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class LockBalanceMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private Lock $lockAlgorithm;

    public function __construct(
        LoggerInterface $logger,
        Lock $lock
    )
    {
        $this->logger = $logger;
        $this->lockAlgorithm = $lock;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $payload = $request->getParsedBody();

        $payer = $payload['payer'];

        try {
            if ($this->lockAlgorithm->isLocked($payer)) {
                throw new FundsLocked();
            }

            $this->lockAlgorithm->lock($payer);

             return $handler->handle($request);
        } catch (\Exception $e) {
            $this->lockAlgorithm->unlock($payer);

            /**
             * Lança novamente a exceção para o error handler tratar
             */
            throw $e;
        } finally {
            $this->lockAlgorithm->unlock($payer);
        }
    }
}

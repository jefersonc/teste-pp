<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Middleware;

use Jefersonc\TestePP\Domain\Transaction\Exception\FundsLocked;
use Jefersonc\TestePP\Infra\Exception\DomainException;
use Jefersonc\TestePP\Infra\Exception\EntityNotFoundException;
use Jefersonc\TestePP\Infra\Exception\NotFoundException;
use Jefersonc\TestePP\Ports\Infra\Lock;
use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
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
        try {
            return $handler->handle($request);
        } catch (DomainException $e) {
            $this->logger->error("Domain exception thrown", [
                "message" => $e->getMessage()
            ]);

            return $this->response(422, $e->getMessage());
        } catch (NotFoundException $e) {
            $this->logger->error("Not found exception thrown", [
                "message" => $e->getMessage()
            ]);

            return $this->response(404, $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical("Unhandled exception thrown", [
                "message" => $e->getMessage()
            ]);

            return $this->response(500, "Internal server error");
        }
    }

    private function response(int $code, string $message): ResponseInterface {
        // todo: tirar dependencia direta do slim
        $response = ResponseFactory::createResponse($code);
        $response->getBody()->write(json_encode([
            "error" => $message
        ]));

        return $response;
    }
}

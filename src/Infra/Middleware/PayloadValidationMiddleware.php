<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Middleware;

use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class PayloadValidationMiddleware implements MiddlewareInterface
{
    private object $scheme;

    public function __construct(string $scheme)
    {
        $this->scheme = json_decode($scheme);
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

        $validator = new Validator;

        $validator->validate($payload, $this->scheme);

        if ($validator->isValid()) {
            return $handler->handle($request);
        }

        // todo: tirar dependencia direta do slim
        $response = ResponseFactory::createResponse(400);
        $errors = $this->formatErrors($validator->getErrors());
        $response->getBody()->write(json_encode($errors));

        return $response;
    }

    private function formatErrors(array $errors): array {
        return array_map(function($error) {
            return [
                'locate' => $error['property'],
                'message' => $error['message']
            ];
        }, $errors);
    }
}

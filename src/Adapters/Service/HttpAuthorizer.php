<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\Service;

use GuzzleHttp\Psr7\Request;
use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Ports\Service\Authorizer;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

final class HttpAuthorizer implements Authorizer
{
    private ClientInterface $client;
    private string $uri;
    private LoggerInterface $logger;

    public function __construct(ClientInterface $client, string $uri, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->uri = $uri;
        $this->logger = $logger;
    }

    public function authorize(Transaction $transaction): bool {
        $this->logger->info("Authorization check validation", [
            'transaction' => $transaction->getId()->getValue()
        ]);

        $request = new Request('get', $this->uri);

        try {
            $response = $this->client->sendRequest($request);

            $payload = $response->getBody()->getContents();

            $this->logger->info("Authorization check completed", [
                'transaction' => $transaction->getId()->getValue(),
                'third_party_response' => $payload
            ]);

            if (200 !== $response->getStatusCode()) {
                $this->logger->error("Authorization check failed", [
                    'transaction' => $transaction->getId()->getValue(),
                    'error' => "Third-Party service returns invalid status code"
                ]);

                return false;
            }

            $payload = json_decode($payload);

            if (!isset($payload->message) || 'Autorizado' !== $payload->message) {
                $this->logger->info("Authorization check rejected", [
                    'transaction' => $transaction->getId()->getValue(),
                    'error' => "This transaction is unauthorized by authorization check."
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error("Authorization check failed", [
                'transaction' => $transaction->getId()->getValue(),
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}

<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\Service;

use GuzzleHttp\Psr7\Request;
use Jefersonc\TestePP\Domain\Transaction\Transaction;
use Jefersonc\TestePP\Ports\Service\Notifier;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class HttpNotifier implements Notifier
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

    public function notify(Transaction $transaction): bool {
        $this->logger->info("Notification", [
            'transaction' => $transaction->getId()->getValue()
        ]);

        $request = new Request('get', $this->uri);

        try {
            $response = $this->client->sendRequest($request);

            $payload = $response->getBody()->getContents();

            $this->logger->info("Notification sent", [
                'transaction' => $transaction->getId()->getValue(),
                'third_party_response' => $payload
            ]);

            if (200 !== $response->getStatusCode()) {
                // todo: pensar em uma deadlatter queue / retentativas
                $this->logger->error("Notification failed", [
                    'transaction' => $transaction->getId()->getValue(),
                    'error' => "Third-Party service returns invalid status code"
                ]);

                return false;
            }

            $payload = json_decode($payload);

            if (!isset($payload->message) || 'Enviado' !== $payload->message) {
                $this->logger->info("Notification not sent", [
                    'transaction' => $transaction->getId()->getValue(),
                    'error' => "The notification service is not able to send notification."
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error("Notification failed", [
                'transaction' => $transaction->getId()->getValue(),
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}

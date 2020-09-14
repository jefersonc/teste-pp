<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\Infra;

use Jefersonc\TestePP\Domain\Customer\Customer;
use Jefersonc\TestePP\Ports\Infra\Lock;
use Psr\Log\LoggerInterface;
use Redis;

/**
 * Class RedisLock
 * @package Jefersonc\TestePP\Adapters\Infra
 */
final class RedisLock implements Lock
{
    private const PREFIX = 'lock';

    /**
     * @var Redis
     */
    private Redis $client;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * RedisLock constructor.
     * @param Redis $client
     * @param LoggerInterface $logger
     */
    public function __construct(Redis $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function isLocked(int $customerId): bool
    {
        $key = $this->getKey($customerId);
        return (bool) $this->client->exists($key);
    }

    /**
     * @param int $customerId
     */
    public function lock(int $customerId): void
    {
        $this->logger->info("Balance locked", [
            'customer' => $customerId
        ]);

        $key = $this->getKey($customerId);
        $this->client->set($key, true);
    }

    /**
     * @param int $customerId
     */
    public function unlock(int $customerId): void
    {
        $this->logger->info("Balance unlocked", [
            'customer' => $customerId
        ]);

        $key = $this->getKey($customerId);
        $this->client->del($key);
    }

    /**
     * @param int $suffix
     * @return string
     */
    private function getKey(int $suffix): string {
        return sprintf('%s_%s', self::PREFIX, $suffix);
    }
}

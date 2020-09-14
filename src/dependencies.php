<?php

declare(strict_types=1);

return [
    \Psr\Log\LoggerInterface::class => function (\DI\Container $c) {
        $log = new \Monolog\Logger($_ENV['LOG_NAME']);
        $handler = new \Monolog\Handler\StreamHandler($_ENV['LOG_PATH'], \Monolog\Logger::INFO);
        $log->pushHandler($handler);

        return $log;
    },
    \Jefersonc\TestePP\Ports\Infra\Lock::class => function (\DI\Container $container) {
        $redis = new Redis();
        $redis->connect(
            $_ENV['REDIS_HOST'],
            (int) $_ENV['REDIS_PORT']
        );

        return new \Jefersonc\TestePP\Adapters\Infra\RedisLock(
            $redis,
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    },
    \Psr\Http\Client\ClientInterface::class => function (\DI\Container $container) {
       return new \GuzzleHttp\Client([
            'timeout'  => 2.0,
        ]);
    },
    \Jefersonc\TestePP\Ports\Service\Authorizer::class => function (\DI\Container $container) {
        return new \Jefersonc\TestePP\Adapters\Service\HttpAuthorizer(
            $container->get(\Psr\Http\Client\ClientInterface::class),
            $_ENV['AUTHORIZER_URI'],
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    },
    \Jefersonc\TestePP\Ports\Service\Notifier::class => function (\DI\Container $container) {
        return new \Jefersonc\TestePP\Adapters\Service\HttpNotifier(
            $container->get(\Psr\Http\Client\ClientInterface::class),
            $_ENV['NOTIFIER_URI'],
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    },
    \MongoDB\Database::class => static function (\DI\Container $container) {
        $dbName = $_ENV['DB_NAME'];

        $query = sprintf(
            "mongodb://%s:%s@%s:%d/%s",
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $dbName
        );

        $client = new \MongoDB\Client($query, array());

        return $client
            ->selectDatabase($dbName);
    },
    \Jefersonc\TestePP\Ports\Repository\TransactionRepository::class => function (\DI\Container $container) {
        return new \Jefersonc\TestePP\Adapters\Repository\TransactionMongoRepository(
            $container->get(\MongoDB\Database::class),
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    },
    \Jefersonc\TestePP\Infra\Middleware\LockBalanceMiddleware::class => function (\DI\Container $container) {
        return new \Jefersonc\TestePP\Infra\Middleware\LockBalanceMiddleware(
            $container->get(\Psr\Log\LoggerInterface::class),
            $container->get(\Jefersonc\TestePP\Ports\Infra\Lock::class)
        );
    },
    \Jefersonc\TestePP\Infra\Middleware\ErrorHandlerMiddleware::class => function (\DI\Container $container) {
        return new \Jefersonc\TestePP\Infra\Middleware\ErrorHandlerMiddleware(
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    }
];

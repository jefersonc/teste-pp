<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\CommandBus;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class CommandBus
{
    private LoggerInterface $logger;
    private ContainerInterface $container;
    private Resolver $resolver;

    public function __construct(
        Resolver $resolver,
        ContainerInterface $container,
        LoggerInterface $logger
    )
    {
        $this->resolver = $resolver;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function dispatch(object $command): void {
        $handlerName = $this->resolver->resolve(get_class($command));
        $handler = $this->container->get($handlerName);

        $handler->handle($command);
    }
}

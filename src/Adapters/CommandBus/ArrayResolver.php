<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Adapters\CommandBus;

use Jefersonc\TestePP\Infra\CommandBus\CommandNotFoundException;
use Jefersonc\TestePP\Infra\CommandBus\Resolver;

class ArrayResolver implements Resolver
{
    private array $definitions;

    public function __construct(array $definitions) {
        $this->definitions = $definitions;
    }

    public function resolve(string $command): string
    {
        if (!isset($this->definitions[$command])) {
            throw new CommandNotFoundException;
        }

        return $this->definitions[$command];
    }
}

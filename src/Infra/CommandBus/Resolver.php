<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\CommandBus;

interface Resolver
{
    public function resolve(string $command): string;
}

<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\CommandBus;

use Jefersonc\TestePP\Infra\Exception\NotFoundException;
use Throwable;

/**
 * Class DomainException
 * @package Jefersonc\TestePP\Infra\Exception
 */
class CommandNotFoundException extends NotFoundException
{
    /**
     * DomainException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Command not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

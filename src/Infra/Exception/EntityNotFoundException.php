<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Exception;

use Exception;
use Throwable;

/**
 * Class DomainException
 * @package Jefersonc\TestePP\Infra\Exception
 */
class EntityNotFoundException extends NotFoundException
{
    /**
     * DomainException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Entity not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

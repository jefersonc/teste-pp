<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction\Exception;

use Jefersonc\TestePP\Infra\Exception\DomainException;
use Throwable;

/**
 * Class InsufficientFunds
 * @package Jefersonc\TestePP\Domain\Transaction\Exception
 */
final class AuthorizationFailed extends DomainException
{
    public function __construct(
        $message = "Authorization failed by third-party validator",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}

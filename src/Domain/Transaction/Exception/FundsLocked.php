<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction\Exception;

use Jefersonc\TestePP\Infra\Exception\DomainException;
use \Throwable;

/**
 * Class FundsLocked
 * @package Jefersonc\TestePP\Domain\Transaction\Exception
 */
final class FundsLocked extends DomainException
{
    public function __construct(
        $message = "The customer funds is locked.",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}

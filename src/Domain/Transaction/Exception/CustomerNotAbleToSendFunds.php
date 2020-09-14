<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Transaction\Exception;

use Jefersonc\TestePP\Infra\Exception\DomainException;
use \Throwable;

/**
 * Class CustomerNotAbleToSendFunds
 * @package Jefersonc\TestePP\Domain\Transaction\Exception
 */
final class CustomerNotAbleToSendFunds extends DomainException
{
    public function __construct(
        $message = "The customer is not able to send funds",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}

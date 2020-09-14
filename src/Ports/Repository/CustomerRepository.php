<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Ports\Repository;

use Jefersonc\TestePP\Domain\Customer\Customer;

interface CustomerRepository
{
    public function findByExternalCode(int $externalCode): ?Customer;
}

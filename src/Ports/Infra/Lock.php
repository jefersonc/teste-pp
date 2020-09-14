<?php

namespace Jefersonc\TestePP\Ports\Infra;

interface Lock
{
    public function isLocked(int $customerId): bool;

    public function lock(int $customerId): void;

    public function unlock(int $customerId): void;
}

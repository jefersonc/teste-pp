<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Common;

trait IteratorCapabilities
{
    private array $list;
    private int $position;

    public function __construct(array $list = []) {
        $this->position = 0;
        $this->list = $list;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->list[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->list[$this->position]);
    }
}

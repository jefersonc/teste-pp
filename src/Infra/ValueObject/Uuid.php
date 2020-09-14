<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\ValueObject;

class Uuid
{
   private string $value;

    /**
     * Uuid constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public static function generate(): Uuid {
        return new self(
            \Ramsey\Uuid\Uuid::uuid4()->toString()
        );
    }
}

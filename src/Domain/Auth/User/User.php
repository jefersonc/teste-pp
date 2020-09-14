<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Domain\Auth\User;

use Jefersonc\TestePP\Infra\ValueObject\Uuid;

final class User
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * User constructor.
     * @param Uuid $id
     * @param string $email
     * @param string $password
     */
    public function __construct(
        string $email,
        string $password
    )
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}

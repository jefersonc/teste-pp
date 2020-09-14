<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\ValueObject;

/**
 * Class Document
 * @package Jefersonc\TestePP\Infra\ValueObject
 *
 * todo: implementar validações de cpf/cnpj
 */
class Document
{
    const CPF = 'CPF';
    const CNPJ = 'CNPJ';

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $number;

    /**
     * Document constructor.
     * @param string $type
     * @param string $number
     */
    public function __construct(string $type, string $number)
    {
        $this->type = $type;
        $this->number = $number;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }
}

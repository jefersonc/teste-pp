<?php

declare(strict_types=1);

namespace Jefersonc\TestePP\Infra\Http\Validation;

class SolicitTransfer
{
    const scheme = <<<'JSON'
    {
        "type": "array",
        "properties": {
            "value": {
                "type": "number",
                "minimum" : 0
            },
            "payer": {
                "type": "integer",
                "minimum" : 0
            },
            "payee": {
                "type": "integer",
                "minimum" : 0
            }
        },
        "required": [
            "value",
            "payer",
            "payee"
        ]
    }
    JSON;
}

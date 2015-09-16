<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;
use exprlib\exceptions\ParsingException;

class Pow extends Scope
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result) || count($result) !== 2) {
            throw new ParsingException('Power must have 2 arguments, ex: power(10,2)');
        }

        return pow($result[0], $result[1]);
    }
}

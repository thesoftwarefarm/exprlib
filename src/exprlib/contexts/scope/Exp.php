<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;
use exprlib\exceptions\ParsingException;

class Exp extends Scope
{
    public function evaluate()
    {
        if (is_array($result = parent::evaluate())) {
            throw new ParsingException('exp accept only one argument');
        }

        return (float) exp($result);
    }
}

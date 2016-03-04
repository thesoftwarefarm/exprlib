<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;
use exprlib\exceptions\ParsingException;

class Min extends Scope
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result)) {
            return $result;
        }

        return min($result);
    }
}

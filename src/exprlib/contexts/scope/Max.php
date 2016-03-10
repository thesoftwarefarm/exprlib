<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\ScopeGroup;
use exprlib\exceptions\ParsingException;

class Max extends ScopeGroup
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result)) {
            return $result;
        }

        return max($result);
    }
}

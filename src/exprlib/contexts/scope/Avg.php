<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\ScopeGroup;
use exprlib\exceptions\ParsingException;

class Avg extends ScopeGroup
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result) || empty($result)) {
            return $result;
        }

        $count = count($result);
        return array_sum($result)/$count;
    }
}

<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;
use exprlib\exceptions\ParsingException;

class Avg extends Scope
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

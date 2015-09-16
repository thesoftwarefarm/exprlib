<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Sqrt extends Scope
{
    public function evaluate()
    {
        return sqrt(parent::evaluate());
    }
}

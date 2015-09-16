<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Sin extends Scope
{
    public function evaluate()
    {
        return sin(deg2rad(parent::evaluate()));
    }
}

<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Tangent extends Scope
{
    public function evaluate()
    {
        return tan(deg2rad(parent::evaluate()));
    }
}

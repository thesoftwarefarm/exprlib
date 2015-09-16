<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Acos extends Scope
{
    public function evaluate()
    {
        return acos(deg2rad(parent::evaluate()));
    }
}

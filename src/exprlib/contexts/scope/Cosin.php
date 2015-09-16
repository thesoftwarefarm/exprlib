<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Cosin extends Scope
{
    public function evaluate()
    {
        return cos(deg2rad(parent::evaluate()));
    }
}

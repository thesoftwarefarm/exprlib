<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;
use exprlib\exceptions\ParsingException;

class Log extends Scope
{
    public function evaluate()
    {
        $result = parent::evaluate();

        if (is_array($result)) {
            if (count($result) != 2) {
                throw new ParsingException('Log accepts only 2 arguments');
            }

            return log($result[0], $result[1]);
        } else {
            $content = (string) $this->content;

            if ($content == 'log(') {
                return log10(parent::evaluate());
            } else { // ln
                return log(parent::evaluate());
            }
        }
    }
}

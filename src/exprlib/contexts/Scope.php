<?php

namespace exprlib\contexts;

use exprlib\Parser;
use exprlib\exceptions\DivisionByZeroException;
use exprlib\exceptions\OutOfScopeException;
use exprlib\exceptions\UnknownTokenException;
use exprlib\contexts\scope;

class Scope implements IfContext
{
    protected $builder;
    protected $childrenContexts = array();
    protected $content;
    protected $operations = array();
    protected $supportedOperations = array('^','/','*','+','-','>','<','=');

    public function __construct($content = null)
    {
        $this->content = $content;
    }

    public function setBuilder(Parser $builder)
    {
        $this->builder = $builder;
    }

    public function addOperation($operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * handle the next token from the tokenized list. example actions
     * on a token would be to add it to the current context expression list,
     * to push a new context on the the context stack, or pop a context off the
     * stack.
     */
    public function handleToken($token)
    {
        $baseToken = $token;
        $token     = strtolower($token);

        if (in_array($token, $this->supportedOperations, true)) {
            $this->addOperation($token);
        } elseif ($token === ',') {
            $context = $this->builder->getContext();

            if (!$context instanceof ScopeGroup) {
                $this->builder->pushContext(new ScopeGroup());
            }

            $this->builder->getContext()
                ->addScopeGroup($this->operations);

            $this->operations = array();
        } elseif ($token === '(') {
            $this->builder->pushContext(new Scope($token));
        } elseif ($token === ')') {

            $scopeOperation = $this->builder->popContext();
            $newContext     = $this->builder->getContext();
            if (is_null($scopeOperation) || (!$newContext)) {
                throw new OutOfScopeException('It misses an open scope');
            }
            $newContext->addOperation($scopeOperation);

        } elseif ($token === 'sin(') {
            $this->builder->pushContext(new scope\Sin($token));
        } elseif ($token === 'acos(') {
            $this->builder->pushContext(new scope\Acos($token));
        } elseif ($token === 'cos(') {
            $this->builder->pushContext(new scope\Cosin($token));
        } elseif ($token === 'sum(') {
            $this->builder->pushContext(new scope\Sum($token));
        } elseif ($token === 'avg(') {
            $this->builder->pushContext(new scope\Avg($token));
        } elseif ($token === 'max(') {
            $this->builder->pushContext(new scope\Max($token));
        } elseif ($token === 'min(') {
            $this->builder->pushContext(new scope\Min($token));
        } elseif ($token === 'tan(') {
            $this->builder->pushContext(new scope\Tangent($token));
        } elseif ($token === 'sqrt(') {
            $this->builder->pushContext(new scope\Sqrt($token));
        } elseif ($token === 'log(' || $token === 'ln(') {
            $this->builder->pushContext(new scope\Log($token));
        } elseif ($token === 'pow(') {
            $this->builder->pushContext(new scope\Pow($token));
        } elseif ($token === 'if(') {
            $this->builder->pushContext(new scope\IfElse($token));
        } elseif ($token === 'exp(') {
            $this->builder->pushContext(new scope\Exp($token));
        } else {
            if (is_numeric($token)) {
                $this->addOperation((float) $token);
            } else {
                throw new UnknownTokenException(sprintf('"%s" is not supported yet', $baseToken));
            }
        }
    }

    /**
     * order of operations:
     * - parentheses, these should all ready be executed before this method is called
     * - exponents, first order
     * - mult/divi, second order
     * - addi/subt, third order
     */
    protected function expressionLoop()
    {
        //@todo refactorize that !
        while (list($i, $operation) = each ($this->operations)) {
            $operators = $this->supportedOperations;
            if (!in_array($operation, $operators, true)) {
                continue;
            }

            // fetch main operator + position of it
            foreach ($operators as $operator) {
                if (false !== $pos = array_search($operator, $this->operations, true)) {
                    $mainOperator = $operator;
                    break;
                }
            }

            $before = array();
            foreach ($this->operations as $key => $value) {
                unset($this->operations[$key]);

                if ($key == $pos) {
                    break;
                }
                $before[] = $value;
            }

            end($before);
            $pos--;
            // * - 10 must regroup -10 for next operation
            if (prev($before) == '-' && in_array(prev($before), $operators)) {
                $pos--;
            }

            $newStack = array();
            $left  = array();
            foreach ($before as $key => $value) {
                if ($key >= $pos) {
                    $left[] = $value;
                } else {
                    $newStack[] = $value;
                }
            }

            $right = array();
            foreach ($this->operations as $key => $value) {
                unset($this->operations[$key]);
                $right[] = $value;
                if (is_numeric($value)) {
                    break;
                }
            }

            $left = count($left) == 1 ? current($left) : implode('', $left);
            $right = count($right) == 1 ? current($right) : implode('', $right);

            $result = null;
            switch ($mainOperator) {
                case '^':
                    $result = pow((float) $left, (float) $right);
                    break;
                case '*':
                    $result = (float) ($left * $right);
                    break;
                case '/':
                    if ($right == 0) {
                        throw new DivisionByZeroException();
                    }

                    $result = (float) ($left / $right);
                    break;
                case '-':
                    $result = (float) ($left - $right);
                    break;
                case '+':
                    $result = (float) ($left + $right);
                    break;
                case '>':
                    $result = ($left > $right);
                    break;
                case '<':
                    $result = ($left < $right);
                    break;
                case '=':
                    $result = ($left == $right);
                    break;
            }

            if ($result) {
                $newStack[] = $result;
            }

            foreach ($this->operations as $operation) {
                $newStack[] = $operation;
            }

            $this->operations = $newStack;
        }

        if (count($this->operations) === 1) {
            return end($this->operations);
        }

        return false;
    }

    # order of operations:
    # - sub scopes first
    # - multiplication, division
    # - addition, subtraction
    # evaluating all the sub scopes (recursivly):
    public function evaluate()
    {
        foreach ($this->operations as $i => $operation) {
            if (is_object($operation)) {
                $this->operations[$i] = $operation->evaluate();
            }
        }
        $operationList = $this->operations;

        while (true) {
            $operationCheck = $operationList;
            $result = $this->expressionLoop();

            if ($result !== false) {
                return $result;
            }

            if ($operationCheck === $operationList) {
                break;
            } else {
                reset($operationList = array_values($operationList));
            }
        }
    }
}

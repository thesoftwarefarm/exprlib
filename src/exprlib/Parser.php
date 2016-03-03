<?php

namespace exprlib;

use exprlib\contexts\IfContext;
use exprlib\contexts\Scope;

/**
 * this model handles the tokenizing, the context stack functions, and
 * the parsing (token list to tree trans).
 * as well as an evaluate method which delegates to the global scopes evaluate.
 */
class Parser
{
    public $precision = 15;
    public $precisionType = null;

    protected $content;
    protected $contextStack = array();
    protected $tree;
    protected $tokens = array();
    protected $vars = array();

    /**
     * @param string $content content
     */
    public function __construct($content = null)
    {
        if (null !== $content) {
            $this->setContent($content);
        }
    }

    /**
     * Allow user to simplify evaluation
     * Parser::build('2+1')->evaluate();
     *
     * @param string  $content       content
     * @param string  $precision     precision
     * @param integer $precisionType precisionType
     *
     * @return Parser
     */
    public static function build($content, $precision = 15, $precisionType = PHP_ROUND_HALF_UP)
    {
        $instance = new static($content);
        $instance->precision = $precision;
        $instance->precisionType = $precisionType;

        return $instance;
    }

    /**
     * this function does some simple syntax cleaning:
     * - removes all spaces
     * - replaces '**' by '^'
     * then it runs a regex to split the contents into tokens. the set
     * of possible tokens in this case is predefined to numbers (ints of floats)
     * math operators (*, -, +, /, **, ^) and parentheses.
     */
    public function tokenize()
    {
        $this->content = str_replace(array("\n","\r","\t"," "), '', $this->content);
        $this->content = str_replace('**', '^', $this->content);
        $this->content = str_replace('PI', (string) PI(), $this->content);
        $this->tokens = preg_split(
            '@
              ([\d\.]+)
              |(
                sin\(
                |log\(
                |ln\(
                |pow\(
                |exp\(
                |acos\(
                |cos\(
                |sum\(
                |avg\(
                |max\(
                |min\(
                |tan\(
                |sqrt\(
                |if\(
                |\+
                |\-
                |\*
                |/
                |\^
                |\(
                |\)
              )
            @ix',
            $this->content,
            null,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        return $this;
    }

    /**
     * this is the the loop that transforms the tokens array into
     * a tree structure.
     */
    public function parse()
    {
        # this is the global scope which will contain the entire tree
        $this->pushContext(new Scope());
        foreach ($this->tokens as $token) {
            # get the last context model from the context stack,
            # and have it handle the next token
            $this->getContext()->handleToken($token);
        }
        $this->tree = $this->popContext();

        return $this;
    }

    public function setVars(array $vars)
    {
        if (count($vars)) {
          $this->vars = array_merge($this->vars, $vars);
        }

        return $this;
    }

    public function evaluate()
    {
        if (count($this->vars)) {
            $this->content = str_replace(array_map(function($varName) {
              return sprintf('{{%s}}', $varName);
            }, array_keys($this->vars)), array_values($this->vars), $this->content);
        }

        if (!$this->tokens) {
            $this->tokenize();
        }

        if (!$this->tree) {
            $this->parse();
        }

        return round($this->tree->evaluate(), $this->precision, $this->precisionType);
    }

    public function setContent($content)
    {
        $this->content = $content;
        // clear tokens
        $this->tokens = array();
        // clear tree
        $this->tree = null;

        return $this;
    }

    /*******************************************************
     * the context stack functions. for the stack im using
     * an array with the functions array_push, array_pop,
     * and end to push, pop, and get the current element
     * from the stack.
     *******************************************************/

    public function pushContext(IfContext $context)
    {
        array_push($this->contextStack, $context);
        $this->getContext()->setBuilder($this);
    }

    public function popContext()
    {
        return array_pop($this->contextStack);
    }

    public function getContext()
    {
        return end($this->contextStack);
    }
}

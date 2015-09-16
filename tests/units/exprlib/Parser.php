<?php

namespace tests\units\exprlib;

require_once __DIR__ . '/../../../vendor/autoload.php';

use mageekguy\atoum;
use exprlib\Parser as ParserModel;

/**
 * Parser
 *
 * @uses atoum\test
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Parser extends atoum\test
{
    public function testExceptions()
    {
        $this->exception(function() { ParserModel::build('ß2+1')->evaluate(); })
        ->isInstanceOf('exprlib\exceptions\UnknownTokenException')
        ->hasMessage('"ß" is not supported yet');

        $this->exception(function() { ParserModel::build('2+1)')->evaluate(); })
        ->isInstanceOf('exprlib\exceptions\OutOfScopeException')
        ->hasMessage('It misses an open scope');

        $this->exception(function() { ParserModel::build('2/0')->evaluate(); })
        ->isInstanceOf('exprlib\exceptions\DivisionByZeroException');
    }

    /**
     * @dataProvider operationsDataProvider
     */
    public function testOperations($operation, $result)
    {
        $this->string((string) ParserModel::build($operation, '5')->evaluate())
            ->isEqualTo((string) $result);
    }

    public function operationsDataProvider()
    {
        $pi4 = M_PI_4;

        return array(
            array('2+4/2+2', 6),
            array('2+4/2', 4),
            array('2+4/-2', 0),
            array('2+-4/-2', 4),
            array('2+-4*-2', 10),
            array('2+1', 3),
            array('2/1', 2),
            array('2/(3.6*8.5)', 0.06536),
            array('2+(6/2)+(8*3)', 29),
            array('2+3+6+6/2+3', 17),
            array('0.001 + 0.02', 0.021),
            array('10*-2', -20),
            array('-10*-2', 20),
            // OPERATIONS
            // cos
            array('COS(0)', 1),
            array('cos(90)', 0),
            array('cos(180)', -1),
            array('cos(360)', 1),
            // sin
            array('sin(0)', 0),
            array('sin(90)', 1),
            array('sin(180)', 0),
            // sqrt
            array('sqrt(9)', 3),
            array('sqrt(4)', 2),
            array('sqrt(3)', 1.73205),
            // tangent
            array(sprintf('tan(%s)', rad2deg($pi4)), 1),
            array('tan(180)', 0),
            // log
            array('log(10)', '1'),
            array('log(10,10)', '1'),
            array('ln(10)', '2.30259'),
            array('log(0.7)', '-0.1549'),
            array('ln(0.7)', '-0.35667'),
            // pow
            array('pow(10, 2)', 100),
            array('pow(10, 3)', 1000),
            array('pow(10, 0)', 1),
            // exp
            array('exp(12)', 162754.79142),
            array('exp(5.7)', 298.8674),
            // sum
            array('sum(10, 20, 30)', 60),
            // avg
            array('avg(10, 20, 30)', 20),
            // special cases
            array('log(0)', -INF),
            array('log(0)*-1', INF),
            array(sprintf('acos(%s)', rad2deg(8)), NAN)
        );
    }
}

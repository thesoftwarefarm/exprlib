exprlib - PHP
=============

[![Build Status](https://secure.travis-ci.org/TheSoftwareFarm/exprlib.png)](http://travis-ci.org/TheSoftwareFarm/exprlib)

This library was forked from rezzza/exprlib, since it was abandoned and we think it's still useful.

An expression parser in PHP, code inspired from [codehackit](http://codehackit.blogspot.fr/2011/08/expression-parser-in-php.html)

An alternative to this is [Hoa/Math](https://github.com/hoaproject/Math)

List of functions and features:

- operators = * + -
- acos
- cos
- sin
- tan
- pow
- sqrt
- log
- exp
- sum
- avg
- min
- max
- if (condition, than, else)
- var placing with {{varName}}

Examples:

```php
<?php
// simple math
exprlib\Parser::build('2+1')->evaluate(); // 3
exprlib\Parser::build('2/1')->evaluate(); // 2
exprlib\Parser::build('2/(3.6*8.5)')->evaluate(); // 0.06536
exprlib\Parser::build('2+(6/2)+(8*3)')->evaluate(); // 29
exprlib\Parser::build('2+3+6+6/2+3')->evaluate(); // 17
exprlib\Parser::build('0.001 + 0.02')->evaluate(); // 0.021

// functions
exprlib\Parser::build('COS(0)')->evaluate(); // 1
exprlib\Parser::build('cos(90)')->evaluate(); // 0
exprlib\Parser::build('cos(180)')->evaluate(); // -1
exprlib\Parser::build('cos(360)')->evaluate(); // 1
exprlib\Parser::build('sin(0)')->evaluate(); // 0
exprlib\Parser::build('sin(90)')->evaluate(); // 1
exprlib\Parser::build('sin(180)')->evaluate(); // 0
exprlib\Parser::build('sqrt(9)')->evaluate(); // 3
exprlib\Parser::build('sqrt(4)')->evaluate(); // 2
exprlib\Parser::build('sqrt(3)')->evaluate(); // 1.73205
exprlib\Parser::build('tan(180)')->evaluate(); // 0
exprlib\Parser::build('log(10)')->evaluate(); // '1'
exprlib\Parser::build('log(10,10)')->evaluate(); // '1'
exprlib\Parser::build('ln(10)')->evaluate(); // '2.30259'
exprlib\Parser::build('log(0.7)')->evaluate(); // '-0.1549'
exprlib\Parser::build('ln(0.7)')->evaluate(); // '-0.35667'
exprlib\Parser::build('pow(10, 2)')->evaluate(); // 100
exprlib\Parser::build('pow(10, 3)')->evaluate(); // 1000
exprlib\Parser::build('pow(10, 0)')->evaluate(); // 1
exprlib\Parser::build('exp(12)')->evaluate(); // 162754.79142
exprlib\Parser::build('exp(5.7)')->evaluate(); // 298.8674
exprlib\Parser::build('sum(10, 20, 30)')->evaluate(); // 60
exprlib\Parser::build('avg(10, 20, 30)')->evaluate(); // 20
exprlib\Parser::build('log(0)')->evaluate(); // -INF
exprlib\Parser::build('log(0)*-1')->evaluate(); // INF
exprlib\Parser::build(sprintf('acos(%s)', rad2deg(8))->evaluate(); // NAN

// min-max
exprlib\Parser::build('max(10,20,30)')->evaluate(); // 30
exprlib\Parser::build('min(10,20,30)')->evaluate(); // 10

// if-elsing
exprlib\Parser::build('if(1=1, 1, 0)')->evaluate() // 1
exprlib\Parser::build('if(1<2, 1, 0)')->evaluate() // 0
exprlib\Parser::build('if(1>2, 1, 0)')->evaluate() // 0

// var placing
exprlib\Parser::build('{{a}}+1')->setVars(array('a' => 3))->evaluate() // 4
exprlib\Parser::build('{{a}}-{{b}}')->setVars(array('a' => 2, 'b' => 5))->evaluate() // -3

// var placing with if-elsing
exprlib\Parser::build('if({{a}}=5, 1, 0)')->setVars(array('a' => 5))->evaluate() // 1
exprlib\Parser::build('if({{a}}>{{b}}, 1, 0)')->setVars(array('a' => 3, 'b' => 2))->evaluate() // 1
```

# Launch tests

Look at .travis.yml

# Todo

+ Look at how is the best way to decouple Scope
+ Add tests

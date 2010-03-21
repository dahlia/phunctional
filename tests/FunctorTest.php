<?php
require_once 'PHPUnit/Extensions/OutputTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';
require_once dirname(__FILE__) . '/../Phunctional/CompositeFunction.php';
require_once dirname(__FILE__) . '/../Phunctional/PartialApplication.php';

final class FunctorTest extends PHPUnit_Extensions_OutputTestCase {
	function __construct() {
		$this->a = rand();
		$this->b = rand();
	}

	function testCallbackToFunction() {
		$functor = Functor('substr');
		$callback = new Callback('substr');

		$this->assertEquals($callback, $functor);
	}

	function testCallbackToMethod() {
		$functor = Functor(array($this, 'testCallbackToMethod'));
		$callback = new Callback($this, 'testCallbackToMethod');

		$this->assertEquals($callback, $functor);
	}

	static function staticMethodForTest() { return true; }

	function testCallbackToStaticMethod() {
		$functor = Functor(array(__CLASS__, 'staticMethodForTest'));
		$callback = new Callback(__CLASS__, 'staticMethodForTest');

		$this->assertEquals($callback, $functor);
	}

	function testCallbackToStaticMethodByString() {
		$functor = Functor(__CLASS__ . '::staticMethodForTest');
		$callback = new Callback(__CLASS__, 'staticMethodForTest');

		$this->assertEquals($callback, $functor);
	}

	function testCallbackToBinaryOperator() {
		$functor = Functor('+');
		$operator = Operator::getInstance('+');

		$this->assertEquals($operator, $functor);
	}

	function testCallbackToUnaryOperator() {
		$functor = Functor('!');
		$operator = Operator::getInstance('!');

		$this->assertEquals($operator, $functor);
	}

	function testRawLambda() {
		$functor = Functor("('raw lambda expression, ' . $0)");
		$expected = new RawLambda("'raw lambda expression, ' . $0");

		$this->assertEquals($expected, $functor);

		$this->assertEquals(
			'raw lambda expression, abc',
			$functor->call('abc')
		);
	}

	function testMemberAccessor() {
		$functor = Functor('->test');
		$memberAccessor = new MemberAccessor('test');

		$this->assertEquals($memberAccessor, $functor);
	}

	function testMethodInvoker() {
		$functor = Functor('->test()');
		$methodInvoker = new MethodInvoker('test');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithIntegerKey() {
		$functor = Functor('[123]');
		$methodInvoker = new ArrayAccessor(123);

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithFloatKey() {
		$functor = Functor('[1.23]');
		$methodInvoker = new ArrayAccessor(1.23);

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithHexadecimalIntegerKey() {
		$functor = Functor('[0xF3]');
		$methodInvoker = new ArrayAccessor(0xF3);

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithOctalIntegerKey() {
		$functor = Functor('[0765]');
		$methodInvoker = new ArrayAccessor(0765);

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithStringKey() {
		$functor = Functor('[test]');
		$methodInvoker = new ArrayAccessor('test');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithSingleQuotedStringKey() {
		$functor = Functor("['test\\n\"key\"']");
		$methodInvoker = new ArrayAccessor('test\n"key"');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithSingleQuotedNumericStringKey() {
		$functor = Functor("['123']");
		$methodInvoker = new ArrayAccessor('123');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithSingleQuotedNumericStringKey2() {
		$functor = Functor("['3.14']");
		$methodInvoker = new ArrayAccessor('3.14');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithDoubleQuotedNumericStringKey() {
		$functor = Functor('["123"]');
		$methodInvoker = new ArrayAccessor('123');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithDoubleQuotedNumericStringKey2() {
		$functor = Functor('["3.14"]');
		$methodInvoker = new ArrayAccessor('3.14');

		$this->assertEquals($methodInvoker, $functor);
	}

	function testArrayAccessorWithDoubleQuotedStringKey() {
		$functor = Functor('["test\n\'key\'"]');
		$methodInvoker = new ArrayAccessor("test\n'key'");

		$this->assertEquals($methodInvoker, $functor);
	}

	function testChainedAccessors() {
		$functor = Functor('[abc]->def->ghi()');
		$accessor = new CompositeFunction(
			new ArrayAccessor('abc'),
			new MemberAccessor('def'),
			new MethodInvoker('ghi')
		);

		$this->assertEquals($accessor, $functor);
	}

	function testChainedAccessors2() {
		$functor = Functor('->abc[123]->def()');
		$accessor = new CompositeFunction(
			new MemberAccessor('abc'),
			new ArrayAccessor(123),
			new MethodInvoker('def')
		);

		$this->assertEquals($accessor, $functor);
	}

	function testChainedAccessors3() {
		$functor = Functor('->abc->def()[789]');
		$accessor = new CompositeFunction(
			new MemberAccessor('abc'),
			new MethodInvoker('def'),
			new ArrayAccessor(789)
		);

		$this->assertEquals($accessor, $functor);
	}

	function testCompositeFunction() {
		$functor = Functor(array(
			null,
			'floatval',
			'round'
		));

		$this->assertType('CompositeFunction', $functor);
		$this->assertEquals(3, $functor->call('3.14'));
	}

	function testCompositeFunction2() {
		$functor = Functor(array(
			'array_unique',
			'count',
			array(
				'dechex',
				new PartialApplication('str_ireplace', array(
					range('a', 'f'),
					range('g', 'l')
				))
			)
		));

		$array = array_merge(range(1, 10), range(5, 30));

		$this->assertType('CompositeFunction', $functor);
		$this->assertEquals('1k', $functor->call($array));
	}

	function testCompositeFunction3() {
		$functor = Functor(array(null, 'date_create'));

		$this->assertType('CompositeFunction', $functor);
		$this->assertType('DateTime', $functor->call('now'));
	}

	function testIdentityFunction() {
		$functor = Functor(null);
		$value = rand();

		$this->assertEquals($value, $functor->call($value));
	}

	function testConstantFunction() {
		$functor = Functor(true);
		$constantFunction = new ConstantFunction(true);

		$this->assertEquals($constantFunction, $functor);
	}

	function testEfficientCallbackToFunctor() {
		$functor = new ConstantFunction(123);
		$callback = Functor(array($functor, 'call'));

		$this->assertSame($functor, $callback);
	}

	protected function runCode($code) {
		if(substr(PHP_OS, 0, 3) == 'WIN') {
			$this->markTestSkipped();
			return;
		}

		$functorPath = dirname(__FILE__) . '/../Phunctional/Functor.php';
		$code = '
			error_reporting(E_ALL);
			require_once ' . var_export($functorPath, true) . ';
		' . trim($code);

		$output = exec('php -n -r ' . escapeshellarg($code), $o, $r);

		if($r === 127)
			$this->markTestSkipped();

		return $output;
	}

	function testExit() {
		$testCode = '
			$exit = Functor("exit");
			$exit->call();
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('', $result);
	}

	function testExitWithMessage() {
		$testCode = '
			$exit = Functor("exit");
			$exit->call("with message");
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('with message', $result);
	}

	function testDie() {
		$testCode = '
			$die = Functor("die");
			$die->call();
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('', $result);
	}

	function testDieWithMessage() {
		$testCode = '
			$die = Functor("die");
			$die->call("with message");
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('with message', $result);
	}

	function testEcho() {
		$echo = Functor('echo');
		$value = rand();

		$this->expectOutputString((string) $value);
		$returns = $echo->call($value);

		$this->assertEquals(null, $returns);
	}

	function testEchoWithManyParameters() {
		$echo = Functor('echo');

		$this->expectOutputString('1234');
		$returns = $echo->call(1, 2, 3, 4);

		$this->assertEquals(null, $returns);
	}

	function testPrint() {
		$print = Functor('print');
		$value = rand();

		$this->expectOutputString((string) $value);
		$returns = $print->call($value);

		$this->assertEquals(1, $returns);
	}

	function testEmpty() {
		$empty = Functor('empty');

		$this->assertTrue($empty->call(''));
		$this->assertTrue($empty->call(0));
		$this->assertTrue($empty->call('0'));
		$this->assertTrue($empty->call(null));
		$this->assertTrue($empty->call(false));
		$this->assertTrue($empty->call(array()));

		$this->assertFalse($empty->call('string'));
		$this->assertFalse($empty->call(1));
		$this->assertFalse($empty->call('1'));
		$this->assertFalse($empty->call(true));
		$this->assertFalse($empty->call(array(1)));
	}

	function testUnaryArithmeticOperatorNegate() {
		$operator = Functor('-');
		$this->assertEquals(-$this->a, $operator->call($this->a));
	}

	function doTestBinaryOperator($operator, $unaryExpected = null) {
		$functor = Functor($operator);
		eval('$value = $this->a ' . $operator . ' $this->b;');

		$this->assertEquals($value, $functor->call($this->a, $this->b));
		$this->assertEquals(is_null($unaryExpected) ? $this->a : $unaryExpected, $functor->call($this->a));

		$c = rand();
		eval('$value = $this->a ' . $operator . ' $this->b ' . $operator . ' $c;');

		$this->assertEquals($value, $functor->call($this->a, $this->b, $c));
	}

	function testBinaryArithmeticOperatorAdd() {
		$this->doTestBinaryOperator('+');
	}

	function testUnaryArithmeticOperatorIncrement() {
		$operator = Functor('++');
		$this->assertEquals($this->a + 1, $operator->call($this->a));
	}

	function testBinaryArithmeticOperatorSubtract() {
		$operator = Functor('-');

		$this->assertEquals(
			$this->a - $this->b,
			$operator->call($this->a, $this->b)
		);

		$value = rand();

		$this->assertEquals(
			$this->a - $this->b - $value,
			$operator->call($this->a, $this->b, $value)
		);
	}

	function testUnaryArithmeticOperatorDecrement() {
		$operator = Functor('--');
		$this->assertEquals($this->a - 1, $operator->call($this->a));
	}

	function testBinaryArithmeticOperatorMultiply() {
		$this->doTestBinaryOperator('*');
	}

	function testBinaryArithmeticOperatorDivide() {
		$this->doTestBinaryOperator('/');
	}

	function testBinaryArithmeticOperatorMod() {
		$this->doTestBinaryOperator('%', 0);
	}

	function testBinaryStringOperatorConcatenation() {
		$operator = Functor('.');
		$this->assertEquals('ab', $operator->call('a', 'b'));
		$this->assertEquals('a', $operator->call('a'));
		$this->assertEquals('abc', $operator->call('a', 'b', 'c'));
	}

	function testUnaryBitwiseOperatorNot() {
		$operator = Functor('~');
		$this->assertEquals(~$this->a, $operator->call($this->a));
	}

	function testBinaryBitwiseOperatorAnd() {
		$this->doTestBinaryOperator('&', 0);
	}

	function testBinaryBitwiseOperatorOr() {
		$this->doTestBinaryOperator('|');
	}

	function testBinaryBitwiseOperatorXor() {
		$this->doTestBinaryOperator('^');
	}

	function testBinaryBitwiseOperatorShiftLeft() {
		$this->doTestBinaryOperator('<<', 0);
	}

	function testBinaryBitwiseOperatorShiftRight() {
		$this->doTestBinaryOperator('>>', 0);
	}

	function testUnaryLogicalOperatorNot() {
		$operator = Functor('!');
		$this->assertFalse($operator->call(true));
		$this->assertTrue($operator->call(false));
	}

	function testBinaryComparingOperatorEqual() {
		$operator = Functor('==');
		$this->assertTrue($operator->call($this->a, $this->a));
		$this->assertTrue($operator->call("$this->a", $this->a));
		$this->assertFalse($operator->call($this->a, -$this->a));
	}

	function testBinaryComparingOperatorIdentical() {
		$operator = Functor('===');
		$this->assertTrue($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertFalse($operator->call($this->a, -$this->a));
	}

	function testBinaryComparingOperatorNotEqual() {
		$operator = Functor('!=');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testBinaryComparingOperatorNotEqual2() {
		$operator = Functor('<>');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testBinaryComparingOperatorNotIdentical() {
		$operator = Functor('!==');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertTrue($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testBinaryComparingOperatorLess() {
		$operator = Functor('<');
		$this->assertTrue($operator->call(1, 2));
		$this->assertFalse($operator->call(1, 1));
		$this->assertFalse($operator->call(2, 1));
	}

	function testBinaryComparingOperatorLessOrEqual() {
		$operator = Functor('<=');
		$this->assertTrue($operator->call(1, 2));
		$this->assertTrue($operator->call(1, 1));
		$this->assertFalse($operator->call(2, 1));
	}

	function testBinaryComparingOperatorGreater() {
		$operator = Functor('>');
		$this->assertFalse($operator->call(1, 2));
		$this->assertFalse($operator->call(1, 1));
		$this->assertTrue($operator->call(2, 1));
	}

	function testBinaryComparingOperatorGreaterOrEqual() {
		$operator = Functor('>=');
		$this->assertFalse($operator->call(1, 2));
		$this->assertTrue($operator->call(1, 1));
		$this->assertTrue($operator->call(2, 1));
	}

	function testBinaryLogicalOperatorAnd($andOperator = 'and') {
		$operator = Functor($andOperator);

		$this->assertTrue($operator->call(true, true));
		$this->assertFalse($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertFalse($operator->call(true, true, false));
		$this->assertFalse($operator->call(true, false, true));
		$this->assertFalse($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}

	function testBinaryLogicalOperatorAnd2() {
		$this->testBinaryLogicalOperatorAnd('&&');
	}

	function testBinaryLogicalOperatorOr($orOperator = 'or') { 
		$operator = Functor($orOperator);

		$this->assertTrue($operator->call(true, true));
		$this->assertTrue($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertTrue($operator->call(true, true, false));
		$this->assertTrue($operator->call(true, false, true));
		$this->assertTrue($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}

	function testBinaryLogicalOperatorOr2() {
		$this->testBinaryLogicalOperatorOr('||');
	}

	function testBinaryLogicalOperatorXor() { 
		$operator = Functor('xor');

		$this->assertFalse($operator->call(true, true));
		$this->assertTrue($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertFalse($operator->call(true, true, false));
		$this->assertFalse($operator->call(true, false, true));
		$this->assertTrue($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}

	function testSimplePartialApplication() {
		$partialApplication = Functor(array(
			'substr' => array(1 => 1, 2 => -1)
		));

		$expected = new PartialApplication('substr', array(1 => 1, 2 => -1));

		$this->assertEquals('ell', $partialApplication->call('hello'));
		$this->assertEquals($expected, $partialApplication);
	}

	function testSimplePartialApplicationWithOperator() {
		$partialApplication = Functor(array('.' => array(1 => '?')));

		$expected = new PartialApplication(
			Operator::getInstance('.'),
			array(1 => '?')
		);

		$this->assertEquals('hello?', $partialApplication->call('hello'));
		$this->assertEquals($expected, $partialApplication);
	}

	function testComplexPartialApplication() {
		$rules = array(
			'functor' => array(0, 'functor', 'function', 'func'),

			'arguments' => array(
				1, 'parameters', 'params', 'arguments', 'args',
				'bindings', 'binding', 'bind', 'curryings', 'currying', 'curry'
			)
		);

		foreach($rules['functor'] as $functorRule) {
			foreach($rules['arguments'] as $argumentsRule) {
				$partialApplication = Functor(array(
					$functorRule => 'substr',
					$argumentsRule => array(1 => 1, 2 => -1)
				));

				$reversedPartialApplication = Functor(array(
					$argumentsRule => array(1 => 1, 2 => -1),
					$functorRule => 'substr'
				));

				$expected = new PartialApplication(
					'substr', array(1 => 1, 2 => -1)
				);

				$this->assertEquals('ell', $partialApplication->call('hello'));
				$this->assertEquals(
					'ell', $reversedPartialApplication->call('hello')
				);
				$this->assertEquals($expected, $partialApplication);
				$this->assertEquals($expected, $reversedPartialApplication);
			}
		}
	}

	function testInstantiation() {
		$expressions = array(
			'new Range', 'new  Range', "new\tRange", "new\nRange", ' new Range',
			'new Range ', "\tnew\r\nRange\n"
		);

		foreach($expressions as $expr) {
			$this->assertType('Instantiation', Functor($expr));
			$this->assertEquals(new Range(2, 9), Functor($expr)->call(2, 9));
		}
	}
}


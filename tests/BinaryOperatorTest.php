<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';

final class BinaryOperatorTest extends PHPUnit_Framework_TestCase {
	protected $a, $b;

	function __construct() {
		$this->a = rand();
		$this->b = rand();
	}

	function doTestBinaryOperator($operator, $unaryExpected = null) {
		$functor = Operator::getInstance($operator);

		eval('$value = $this->a ' . $operator . ' $this->b;');
		$this->assertEquals(
			$value,
			$result = $functor->call($this->a, $this->b),
			"Assertion: $value == {$this->a} $operator {$this->b}, returns $result"
		);

		$unaryExpected = is_null($unaryExpected) ? $this->a : $unaryExpected;
		$this->assertEquals(
			$unaryExpected,
			$result = $functor->call($this->a),
			"Assertion: $unaryExpected == $operator({$this->a}), returns $result"
		);

		$c = rand();
		eval('$value = $this->a ' . $operator . ' $this->b ' . $operator . ' $c;');

		$this->assertEquals(
			$value,
			$result = $functor->call($this->a, $this->b, $c),
			"Assertion: $value == {$this->a} $operator {$this->b} $operator $c, returns $result"
		);
	}

	function testArithmeticOperatorAdd() {
		$this->doTestBinaryOperator('+');
	}

	function testArithmeticOperatorMultiply() {
		$this->doTestBinaryOperator('*');
	}

	function testArithmeticOperatorDivide() {
		$this->doTestBinaryOperator('/');
	}

	function testArithmeticOperatorMod() {
		$this->doTestBinaryOperator('%', 0);
	}

	function testStringOperatorConcatenation() {
		$operator = Operator::getInstance('.');
		$this->assertEquals('ab', $operator->call('a', 'b'));
		$this->assertEquals('a', $operator->call('a'));
		$this->assertEquals('abc', $operator->call('a', 'b', 'c'));
	}

	function testBitwiseOperatorAnd() {
		$this->doTestBinaryOperator('&', 0);
	}

	function testBitwiseOperatorOr() {
		$this->doTestBinaryOperator('|');
	}

	function testBitwiseOperatorXor() {
		$this->doTestBinaryOperator('^');
	}

	function testBitwiseOperatorShiftLeft() {
		$this->doTestBinaryOperator('<<', 0);
	}

	function testBitwiseOperatorShiftRight() {
		$this->doTestBinaryOperator('>>', 0);
	}

	function testComparingOperatorEqual() {
		$operator = Operator::getInstance('==');
		$this->assertTrue($operator->call($this->a, $this->a));
		$this->assertTrue($operator->call("$this->a", $this->a));
		$this->assertFalse($operator->call($this->a, -$this->a));
	}

	function testComparingOperatorIdentical() {
		$operator = Operator::getInstance('===');
		$this->assertTrue($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertFalse($operator->call($this->a, -$this->a));
	}

	function testComparingOperatorNotEqual() {
		$operator = Operator::getInstance('!=');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testComparingOperatorNotEqual2() {
		$operator = Operator::getInstance('<>');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertFalse($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testComparingOperatorNotIdentical() {
		$operator = Operator::getInstance('!==');
		$this->assertFalse($operator->call($this->a, $this->a));
		$this->assertTrue($operator->call("$this->a", $this->a));
		$this->assertTrue($operator->call($this->a, -$this->a));
	}

	function testComparingOperatorLess() {
		$operator = Operator::getInstance('<');
		$this->assertTrue($operator->call(1, 2));
		$this->assertFalse($operator->call(1, 1));
		$this->assertFalse($operator->call(2, 1));
	}

	function testComparingOperatorLessOrEqual() {
		$operator = Operator::getInstance('<=');
		$this->assertTrue($operator->call(1, 2));
		$this->assertTrue($operator->call(1, 1));
		$this->assertFalse($operator->call(2, 1));
	}

	function testComparingOperatorGreater() {
		$operator = Operator::getInstance('>');
		$this->assertFalse($operator->call(1, 2));
		$this->assertFalse($operator->call(1, 1));
		$this->assertTrue($operator->call(2, 1));
	}

	function testComparingOperatorGreaterOrEqual() {
		$operator = Operator::getInstance('>=');
		$this->assertFalse($operator->call(1, 2));
		$this->assertTrue($operator->call(1, 1));
		$this->assertTrue($operator->call(2, 1));
	}

	function testLogicalOperatorAnd($andOperator = 'and') {
		$operator = Operator::getInstance($andOperator);

		$this->assertTrue($operator->call(true, true));
		$this->assertFalse($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertFalse($operator->call(true, true, false));
		$this->assertFalse($operator->call(true, false, true));
		$this->assertFalse($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}

	function testLogicalOperatorAnd2() {
		$this->testLogicalOperatorAnd('&&');
	}

	function testLogicalOperatorOr($orOperator = 'or') { 
		$operator = Operator::getInstance($orOperator);

		$this->assertTrue($operator->call(true, true));
		$this->assertTrue($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertTrue($operator->call(true, true, false));
		$this->assertTrue($operator->call(true, false, true));
		$this->assertTrue($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}

	function testLogicalOperatorOr2() {
		$this->testLogicalOperatorOr('||');
	}

	function testLogicalOperatorXor() { 
		$operator = Operator::getInstance('xor');

		$this->assertFalse($operator->call(true, true));
		$this->assertTrue($operator->call(true, false));
		$this->assertFalse($operator->call(false, false));

		$this->assertTrue($operator->call(true, true, true));
		$this->assertFalse($operator->call(true, true, false));
		$this->assertFalse($operator->call(true, false, true));
		$this->assertTrue($operator->call(true, false, false));
		$this->assertFalse($operator->call(false, false, false));
	}
}


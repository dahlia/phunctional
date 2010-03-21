<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';

final class UnaryOperatorTest extends PHPUnit_Framework_TestCase {
	function assertUnaryOperator($operator, $value) {
		$operand = $value;

		eval('$expected = ' . $operator . ' $operand;');
		$functor = Operator::getInstance($operator);

		$this->assertEquals(
			$expected, $result = $functor->call($value),
			"Assertion: $expected == $operator $value, result: $result"
		);
	}

	function testLogicalNegateOperator() {
		$this->assertUnaryOperator('!', true);
		$this->assertUnaryOperator('!', false);
	}

	function testBitwiseNotOperator() {
		$this->assertUnaryOperator('~', 0);
		$this->assertUnaryOperator('~', 123);
		$this->assertUnaryOperator('~', M_PI);
		$this->assertUnaryOperator('~', 123.456);
	}

	function testDecrementalOperator() {
		$this->assertUnaryOperator('--', 1);
		$this->assertUnaryOperator('--', -123);
		$this->assertUnaryOperator('--', M_PI);
	}

	function testIncrementalOperator() {
		$this->assertUnaryOperator('++', 1);
		$this->assertUnaryOperator('++', -123);
		$this->assertUnaryOperator('++', M_PI);
	}
}


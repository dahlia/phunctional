<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';

final class MinusOperatorTest extends PHPUnit_Framework_TestCase {
	protected $operator, $a, $b;

	function __construct() {
		$this->operator = Operator::getInstance('-');
		$this->a = rand();
		$this->b = rand();
	}

	function testNegate() {
		$expected = -$this->a;
		$value = $this->operator->call($this->a);

		$this->assertEquals($expected, $value);
	}

	function testSubtract() {
		$expected = $this->a - $this->b;
		$value = $this->operator->call($this->a, $this->b);

		$this->assertEquals($expected, $value);
	}

	function testSubtractWith3Parameters() {
		$c = rand();
		$expected = $this->a - $this->b - $c;
		$value = $this->operator->call($this->a, $this->b, $c);

		$this->assertEquals($expected, $value);

	}

	function testSubtractWith4Parameters() {
		$c = rand();
		$d = rand();

		$expected = $this->a - $this->b - $c - $d;
		$value = $this->operator->call($this->a, $this->b, $c, $d);

		$this->assertEquals($expected, $value);

	}
}


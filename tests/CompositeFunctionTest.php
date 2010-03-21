<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/CompositeFunction.php';

final class CompositeFunctionTest extends PHPUnit_Framework_TestCase {
	protected $from, $to;

	function __construct() {
		$this->from = rand(1, 10);
		$this->to = rand(15, 25);
		$this->expected = array_reverse(range($this->from, $this->to));
	}

	function testTwoFunctor() {
		$reversedRange = new CompositeFunction('range', 'array_reverse');
		$result = $reversedRange->call($this->from, $this->to);

		$this->assertEquals($this->expected, $result);
	}

	function testThreeFunctor() {
		$reversedRange = new CompositeFunction('range', 'array_reverse', 'count');
		$result = $reversedRange->call($this->from, $this->to);

		$this->assertEquals(count($this->expected), $result);

	}

	function testIdentityFunction() {
		$compfunc = new CompositeFunction(
			null,
			'(count($_) > 1 ? $_ : $_)'
		);

		$this->assertEquals(array(true), $compfunc->call(true, true));
	}
}


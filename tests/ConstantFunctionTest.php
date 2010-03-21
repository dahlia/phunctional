<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class ConstantFunctionTest extends PHPUnit_Framework_TestCase {
	protected $fixture, $value;

	function __construct() {
		$this->value = rand();
		$this->fixture = new ConstantFunction($this->value);
	}

	function testInstantiate() {
		$this->assertEquals($this->value, $this->fixture->value);
	}

	function testCall() {
		$result = $this->fixture->call();
		$this->assertEquals($this->value, $result);
	}
}


<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class MethodInvokerTest extends PHPUnit_Framework_TestCase {
	protected $fixture, $method;

	function __construct() {
		$this->method = 'methodForTest';
		$this->fixture = new MethodInvoker($this->method);
	}

	function testInstantiate() {
		$this->assertEquals($this->method, $this->fixture->method);
	}

	function methodForTest() {
		static $value = null;
		if(is_null($value))
			$value = rand();
		return $value;
	}

	function testCall() {
		$result = $this->fixture->call($this);
		$value = $this->{$this->method}();

		$this->assertEquals($value, $result);
	}
}


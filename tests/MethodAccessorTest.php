<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class MethodAccessorTest extends PHPUnit_Framework_TestCase {
	protected $fixture, $method;

	function __construct() {
		$this->method = 'methodForTest';
		$this->fixture = new MethodAccessor($this->method);
	}

	function testInstantiate() {
		$this->assertEquals($this->method, $this->fixture->method);
	}

	function methodForTest() { return true; }

	function testCall() {
		$result = $this->fixture->call($this);
		$callback = new Callback($this, $this->method);

		$this->assertEquals($callback, $result);
	}
}


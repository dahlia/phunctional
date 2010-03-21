<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class ArrayAccessorTest extends PHPUnit_Framework_TestCase {
	protected $array, $fixture;
	protected $key = 0;

	function __construct() {
		$this->array = array(rand());
		$this->fixture = new ArrayAccessor($this->key);
	}

	function testInstantiate() {
		$this->assertEquals($this->key, $this->fixture->key);
	}

	function testCall() {
		$result = $this->fixture->call($this->array);
		$expected = $this->array[$this->key];
		$this->assertEquals($expected, $result);
	}
}


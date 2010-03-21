<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/types.php';

final class TypesTest extends PHPUnit_Framework_TestCase {
	protected $values = array(
		true, false,
		'string', '', ' ',
		'0', '1', '3.14',
		0, 1, 3.14,
		array(1, 2, 3),
		array(), null
	);

	function assertTypes($expectedType, $values) {
		foreach($values as $value)
			$this->assertType($expectedType, $value);
	}

	function testBool() {
		$results = array_map('bool', $this->values);
		$this->assertTypes('bool', $results);
	}

	function testBoolean() {
		$results = array_map('boolean', $this->values);
		$this->assertTypes('bool', $results);
	}

	function testInt() {
		$results = array_map('int', $this->values);
		$this->assertTypes('int', $results);
	}

	function testInteget() {
		$results = array_map('integer', $this->values);
		$this->assertTypes('int', $results);
	}

	function testFloat() {
		$results = array_map('float', $this->values);
		$this->assertTypes('float', $results);
	}

	function testDouble() {
		$results = array_map('double', $this->values);
		$this->assertTypes('float', $results);
	}

	function testReal() {
		$results = array_map('real', $this->values);
		$this->assertTypes('float', $results);
	}

	function testString() {
		$results = array_map('string', $this->values);
		$this->assertTypes('string', $results);
	}

	function testArray() {
		$results = array_map('array_', $this->values);
		$this->assertTypes('array', $results);
	}

	function testObject() {
		$results = array_map('object', $this->values);
		$this->assertTypes('object', $results);
	}
}


<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/FunctionArray.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';

final class FunctionArrayTest extends PHPUnit_Framework_TestCase {
	function testInitialize() {
		$array = new FunctionArray;
		$this->assertEquals(0, count($array));
	}

	function testInitializeWithArray() {
		$array = new FunctionArray(array('is_int', 'is_string'));
		$this->assertEquals(2, count($array));
		$this->assertEquals(new Callback('is_int'), $array[0]);
		$this->assertEquals(new Callback('is_string'), $array[1]);
	}

	function testInitializeWithSequence() {
		$array = new FunctionArray(new Range(1, 2));
		$this->assertEquals(2, count($array));
		$this->assertEquals(new ConstantFunction(1), $array[0]);
		$this->assertEquals(new ConstantFunction(2), $array[1]);
	}

	function testSet() {
		$array = new FunctionArray;

		$array[] = 'is_int';
		$this->assertEquals(1, count($array));
		$this->assertEquals(new Callback('is_int'), $array[0]);

		$array[0] = null;
		$this->assertEquals(1, count($array));
		$this->assertEquals(IdentityFunction::getInstance(), $array[0]);
	}

	function testAppend() {
		$array = new FunctionArray;

		$array->append('is_int');
		$this->assertEquals(1, count($array));
		$this->assertEquals(new Callback('is_int'), $array[0]);
	}
}


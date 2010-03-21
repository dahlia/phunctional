<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';

final class InstantiationTest extends PHPUnit_Framework_TestCase {
	protected $fixture, $class;

	function __construct() {
		$this->class = 'Range';
		$this->fixture = new Instantiation($this->class);
	}

	function testClass() {
		$this->assertEquals($this->class, $this->fixture->class);
	}

	function testInstantiate() {
		$result = $this->fixture->call(5, 10);
		$expected = new Range(5, 10);

		$this->assertEquals($expected, $result);
	}

	function testException() {
		try {
			new Instantiation('NotExistingClass');
			$this->fail();
		}
		catch(UnexpectedValueException $e) {
			return;
		}
	}
}


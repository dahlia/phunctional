<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/EventHandler.php';
require_once dirname(__FILE__) . '/../Phunctional/PartialApplication.php';

final class EventHandlerTest extends PHPUnit_Framework_TestCase {
	protected $eventHandler;

	function __construct() {
		$this->eventHandler = new EventHandler;

		$this->eventHandler[] = 'is_int';
		$this->eventHandler[] = 'is_string';
		$this->eventHandler[] = new PartialApplication(
			'substr', array(1 => 1, 2 => -1)
		);
	}

	function testCall() {
		$string = 'The best way to predict the future is to invent it.';
		$results = $this->eventHandler->call($string);
		$expected = array(false, true, substr($string, 1, -1));

		$this->assertEquals($expected, $results);
	}

	function testGet() {
		$this->assertEquals(new Callback('is_int'), $this->eventHandler[0]);
	}

	function testGetFromLast() {
		$this->assertType('PartialApplication', $this->eventHandler[-1]);
	}

	function testSet() {
		$eventHandler = new EventHandler;
		$this->assertEquals(0, count($eventHandler));
		$eventHandler[] = 'is_int';
		$this->assertEquals(1, count($eventHandler));
		$this->assertEquals(new Callback('is_int'), $eventHandler[-1]);
	}

	function testCount() {
		$this->assertEquals(3, count($this->eventHandler));
	}

	function testIsset() {
		$this->assertTrue(isset($this->eventHandler[0]));
		$this->assertTrue(isset($this->eventHandler[1]));
		$this->assertTrue(isset($this->eventHandler[2]));
		$this->assertFalse(isset($this->eventHandler[3]));
	}

	function testIssetFromLast() {
		$this->assertTrue(isset($this->eventHandler[-1]));
		$this->assertTrue(isset($this->eventHandler[-2]));
		$this->assertTrue(isset($this->eventHandler[-3]));
		$this->assertFalse(isset($this->eventHandler[-4]));
	}

	function testGetIterator() {
		$this->assertType('Iterator', $this->eventHandler->getIterator());
	}

	function testIteratorWorks() {
		$expected = array();
		for($i = 0; $i < 3; ++$i)
			$expected[] = $this->eventHandler[$i];

		$this->assertEquals($expected, iterator_to_array($this->eventHandler));
	}
}


<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/PartialApplication.php';

final class PartialApplicationTest extends PHPUnit_Framework_TestCase {
	protected $function, $offset, $length, $expected;
	protected $string = 'Es irrt der Mensch, solang er strebt.';

	function __construct() {
		$this->offset = rand(1, strlen($this->string) - 5);
		$this->length = rand($this->offset + 1, strlen($this->string) - 3);

		$this->expected = substr($this->string, $this->offset, $this->length);
	}

	function testBindNothing() {
		$binding = new PartialApplication('substr');
		$result = $binding->call($this->string, $this->offset, $this->length);

		$this->assertEquals($this->expected, $result);
	}

	function testBind1st() {
		$binding = new PartialApplication('substr', array($this->string));
		$result = $binding->call($this->offset, $this->length);

		$this->assertEquals($this->expected, $result);
	}

	function testBind2nd() {
		$binding = new PartialApplication('substr', array(1 => $this->offset));
		$result = $binding->call($this->string, $this->length);

		$this->assertEquals($this->expected, $result);
	}

	function testBind3rd() {
		$binding = new PartialApplication('substr', array(2 => $this->length));
		$result = $binding->call($this->string, $this->offset);

		$this->assertEquals($this->expected, $result);
	}

	function testBind1st2nd() {
		$binding = new PartialApplication(
			'substr', array($this->string, $this->offset)
		);
		$result = $binding->call($this->length);

		$this->assertEquals($this->expected, $result);
	}

	function testBind1st3rd() {
		$binding = new PartialApplication(
			'substr', array($this->string, 2 => $this->length)
		);
		$result = $binding->call($this->offset);

		$this->assertEquals($this->expected, $result);
	}

	function testBind2nd3rd() {
		$binding = new PartialApplication(
			'substr', array(1 => $this->offset, 2 => $this->length)
		);
		$result = $binding->call($this->string);

		$this->assertEquals($this->expected, $result);
	}

	function testBindAll() {
		$binding = new PartialApplication(
			'substr', array($this->string, $this->offset, $this->length)
		);
		$result = $binding->call();

		$this->assertEquals($this->expected, $result);
	}
}


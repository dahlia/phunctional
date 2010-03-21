<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Transformation.php';
require_once dirname(__FILE__) . '/../Phunctional/Mappable.php';

final class MappableImplementation implements IteratorAggregate, Mappable {
	function __construct($iterable) {
		$this->iterator = Iterator($iterable);
	}

	function getIterator() {
		return $this->iterator;
	}

	function map($callable) {
		return $this;
	}
}

final class TransformationTest extends PHPUnit_Framework_TestCase {
	static function callback($value) {
		return $value + 1;
	}

	function testMap() {
		$callback = array(__CLASS__, 'callback');
		$source = range(10, 25);
		$map = new Transformation($callback, $source);
		$expected = array_map($callback, $source);

		$this->assertEquals($expected, iterator_to_array($map));
	}

	function testMappable() {
		$callback = array(__CLASS__, 'callback');
		$source = new MappableImplementation(range(10, 25));
		$map = new Transformation($callback, $source);
		$expected = $source->map($callback);

		$this->assertEquals(
			iterator_to_array($expected),
			iterator_to_array($map)
		);
	}
}


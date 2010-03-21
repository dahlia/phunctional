<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Filter.php';
require_once dirname(__FILE__) . '/../Phunctional/Filterable.php';

final class FilterableImplementation implements IteratorAggregate, Filterable {
	function __construct($iterable) {
		$this->iterator = Iterator($iterable);
	}

	function getIterator() {
		return $this->iterator;
	}

	function filter($callable) {
		return $this;
	}
}

final class FilterTest extends PHPUnit_Framework_TestCase {
	static function callback($value) {
		return $value % 2;
	}

	function testMap() {
		$callback = array(__CLASS__, 'callback');
		$source = range(10, 25);
		$filter = new Filter($callback, $source);
		$expected = array_filter($source, $callback);

		$this->assertEquals($expected, iterator_to_array($filter));
	}

	function testFilterable() {
		$callback = array(__CLASS__, 'callback');
		$source = new FilterableImplementation(range(10, 25));
		$filter = new Filter($callback, $source);
		$expected = $source->filter($callback);

		$this->assertEquals(
			(iterator_to_array($expected)),
			(iterator_to_array($filter))
		);
	}
}


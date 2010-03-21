<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Iterator.php';

final class TestIteratorAggregate implements IteratorAggregate {
	public $evaluated = false;

	function getIterator() {
		$this->evaluated = true;
		return new ArrayIterator(array(1, 2, 3));
	}
}

final class IteratorAggregateIteratorTest extends SequenceTestCase {
	function testLaziness() {
		$it = new TestIteratorAggregate;
		new IteratorAggregateIterator($it);
		$this->assertFalse($it->evaluated);
	}

	function testIterate() {
		$this->assertSequenceEquals(
			array(1, 2, 3),
			new IteratorAggregateIterator(new TestIteratorAggregate)
		);
	}
}

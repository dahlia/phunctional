<?php
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';
require_once dirname(__FILE__) . '/../Phunctional/Reversible.php';

final class RangeTest extends SequenceTestCase {
	function assertRange() {
		$params = func_get_args();
		$expected = call_user_func_array('range', $params);
		$newRange = new Instantiation('Range');
		$value = iterator_to_array($newRange->apply($params));

		$this->assertEquals($expected, $value);
	}

	function testSimpleRange() {
		$this->assertRange(1, 10);
		$this->assertRange(5, 20);
	}

	function testSimpleRangeWithStep() {
		$this->assertRange(1, 10, 2);
		$this->assertRange(5, 20, 3);
	}

	function testSimpleRangeWithNegativeStep() {
		$this->assertRange(1, 10, -2);
		$this->assertRange(5, 20, -3);
	}

	function testReversedRange() {
		$this->assertRange(10, 1);
		$this->assertRange(20, 5);
	}

	function testReversedRangeWithStep() {
		$this->assertRange(10, 1, 2);
		$this->assertRange(20, 5, 3);
	}

	function testReversedRangeWithNegativeStep() {
		$this->assertRange(10, 1, -2);
		$this->assertRange(20, 5, -3);
	}

	function testCharacterRange() {
		$this->assertRange('a', 'f');
		$this->assertRange('o', 'z');
	}

	function testCharacterRangeWithStep() {
		$this->assertRange('a', 'f', 2);
		$this->assertRange('o', 'z', 3);
	}

	function testCharacterRangeWithNegativeStep() {
		$this->assertRange('a', 'f', -2);
		$this->assertRange('o', 'z', -3);
	}

	function testReversedCharacterRange() {
		$this->assertRange('f', 'a');
		$this->assertRange('z', 'o');
	}

	function testReversedCharacterRangeWithStep() {
		$this->assertRange('f', 'a', 2);
		$this->assertRange('z', 'o', 3);
	}

	function testReversedCharacterRangeWithNegativeStep() {
		$this->assertRange('f', 'a', -2);
		$this->assertRange('z', 'o', -3);
	}

	function testReverse() {
		$range = new Range(5, 10);
		$this->assertType('Reversible', $range);
		$this->assertSequenceEquals(range(10, 5), $range->reverse());

		$range = new Range(10, 5);
		$this->assertSequenceEquals(range(5, 10), $range->reverse());

		$range = new Range(30, 7, 4);
		$this->assertSequenceEquals(range(10, 30, 4), $range->reverse());

		$range = new Range(7, 30, 4);
		$this->assertSequenceEquals(range(27, 7, 4), $range->reverse());

		$range = new Range('c', 'g');
		$this->assertSequenceEquals(range('g', 'c'), $range->reverse());

		$range = new Range('g', 'c');
		$this->assertSequenceEquals(range('c', 'g'), $range->reverse());

		$range = new Range('s', 'e', 4);
		$this->assertSequenceEquals(range('g', 's', 4), $range->reverse());

		$range = new Range('e', 's', 4);
		$this->assertSequenceEquals(range('q', 'e', 4), $range->reverse());
	}
}


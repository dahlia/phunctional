<?php
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/PaddingIterator.php';
require dirname(__FILE__) . '/../Phunctional/shortcuts.php';

final class PaddingIteratorTest extends SequenceTestCase {
	var $size = 10;
	var $value = 'a';

	function testFromArray() {
		$src = array(122, 123, 124);
		$expected = array_pad($src, $this->size, $this->value);

		$padded = new PaddingIterator($src, $this->size, $this->value);
		$this->assertSequenceEquals($expected, $padded);
		$this->assertEquals(count($expected), count($padded));

		$expected = array_pad($src, -$this->size, $this->value);
		$padded = new PaddingIterator($src, -$this->size, $this->value);

		$this->assertSequenceEquals($expected, $padded);
		$this->assertEquals(count($expected), count($padded));
	}

	function testFromIterator() {
		$src = array(122, 123, 124);
		$expected = array_pad($src, $this->size, $this->value);

		$padded = new PaddingIterator(
			new ArrayIterator($src),
			$this->size, $this->value
		);
		$this->assertSequenceEquals($expected, $padded);

		$expected = array_pad($src, -$this->size, $this->value);

		$padded = new PaddingIterator(
			new ArrayIterator($src),
			-$this->size, $this->value
		);
		$this->assertSequenceEquals($expected, $padded);
	}

	function testPass() {
		$expected = array(122, 123, 124);

		for($i = -count($expected); $i < count($expected); ++$i) {
			$this->assertSequenceEquals(
				$expected,
				new PaddingIterator($expected, $i, 0)
			);
		}
	}

	function testShortcut() {
		$src = array(1, 2, 3);
		$byShortcut = pad($src, $this->size, $this->value);
		$expected = new PaddingIterator($src, $this->size, $this->value);

		$this->assertEquals($expected, $byShortcut);
	}
}

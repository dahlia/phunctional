<?php
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/KeyIterator.php';
require dirname(__FILE__) . '/../Phunctional/shortcuts.php';

final class KeyIteratorTest extends SequenceTestCase {
	function testFromArray() {
		$src = array(122, 123, 124);
		$expectedKeys = array_keys($src);
		$keys = new KeyIterator($src);
		$this->assertSequenceEquals($expectedKeys, $keys);

		$src = array('a' => 122, 'b' => 123, 'c' => 124);
		$expectedKeys = array_keys($src);
		$keys = new KeyIterator($src);
		$this->assertSequenceEquals($expectedKeys, $keys);
	}

	function testFromIterator() {
		$src = new ArrayIterator(array(122, 123, 124));
		$expectedKeys = array(0, 1, 2);
		$keys = new KeyIterator($src);

		$this->assertSequenceEquals($expectedKeys, $keys);
	}

	function testShortcut() {
		$src = array(1, 2, 3);
		$byShortcut = keys($src);
		$expected = new KeyIterator($src);

		$this->assertEquals($expected, $byShortcut);
	}
}

<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Zip.php';

final class ZipTest extends PHPUnit_Framework_TestCase {
	function assertSequenceEquals($expected, $iterable) {
		if($expected instanceof Traversable)
			$expected = iterator_to_array($expected);
		if($iterable instanceof Traversable)
			$iterable = iterator_to_array($iterable);

		$this->assertEquals($expected, $iterable, 'Sequences do not equal.');
	}

	function testZipNothing() {
		$zip = new Zip;
		$this->assertSequenceEquals(array(), $zip);
	}

	function testZipOne() {
		$zip = new Zip(array(range(1, 10)));

		$expected = array_map(
			create_function('$i', 'return array($i);'),
			range(1, 10)
		);

		$this->assertSequenceEquals($expected, $zip);
	}

	function testZipOneWithKey() {
		$zip = new Zip(array('key' => range(1, 10)));

		$expected = array_map(
			create_function('$i', 'return array("key" => $i);'),
			range(1, 10)
		);

		$this->assertSequenceEquals($expected, $zip);
	}

	function testZipTwo() {
		$zip = new Zip(array(range(1, 10), range(20, 25)));

		$expected = array(
			array(1, 20),
			array(2, 21),
			array(3, 22),
			array(4, 23),
			array(5, 24),
			array(6, 25)
		);

		$this->assertSequenceEquals($expected, $zip);
	}

	function testZipTwoWithKeys() {
		$zip = new Zip(array('a' => range(1, 10), 'b' => range(20, 25)));

		$expected = array(
			array('a' => 1, 'b' => 20),
			array('a' => 2, 'b' => 21),
			array('a' => 3, 'b' => 22),
			array('a' => 4, 'b' => 23),
			array('a' => 5, 'b' => 24),
			array('a' => 6, 'b' => 25)
		);

		$this->assertSequenceEquals($expected, $zip);
	}

	function testZipThree() {
		$zip = new Zip(array(range(1, 10), range(20, 25), range(30, 35)));

		$expected = array(
			array(1, 20, 30),
			array(2, 21, 31),
			array(3, 22, 32),
			array(4, 23, 33),
			array(5, 24, 34),
			array(6, 25, 35)
		);

		$this->assertSequenceEquals($expected, $zip);
	}

	function testZipThreeWithKeys() {
		$zip = new Zip(array(
			'a' => range(1, 10),
			'b' => range(20, 25),
			'c' => range(30, 35)
		));

		$expected = array(
			array('a' => 1, 'b' => 20, 'c' => 30),
			array('a' => 2, 'b' => 21, 'c' => 31),
			array('a' => 3, 'b' => 22, 'c' => 32),
			array('a' => 4, 'b' => 23, 'c' => 33),
			array('a' => 5, 'b' => 24, 'c' => 34),
			array('a' => 6, 'b' => 25, 'c' => 35)
		);

		$this->assertSequenceEquals($expected, $zip);
	}
}


<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Iterator.php';
require_once dirname(__FILE__) . '/../Phunctional/StringIterator.php';

final class NotIterable {}

final class IteratorTest extends SequenceTestCase {
	function testException() {
		$invalidValues = array(1, 3.14, null, true, false, new NotIterable);

		foreach($invalidValues as $value) {
			$message = 'Passed value: ' . var_export($value, true);

			try {
				$e = null;
				Iterator($value);
			}
			catch(Exception $e) {
				$this->assertType('InvalidArgumentException', $e, $message);
				return;
			}

			$this->fail();
		}
	}

	function testArray() {
		$array = range(1, 10);
		$iterator = Iterator($array);

		$this->assertType('Iterator', $iterator);
		$this->assertSequenceEquals($array, $iterator);
	}

	function testIteratorAggregate() {
		$iteratorAggregate = new ArrayObject(range(1, 5));
		$iterator = Iterator($iteratorAggregate);

		$this->assertType('Iterator', $iterator);
		$this->assertSequenceEquals($iteratorAggregate, $iterator);
	}

	function testIterator() {
		$expectedIterator = new ArrayIterator(range(1, 5));
		$iterator = Iterator($expectedIterator);

		$this->assertType('Iterator', $iterator);
		$this->assertSequenceEquals($expectedIterator, $iterator);
	}

	function testString() {
		$expectedIterator = new StringIterator("\xea\xb0\x80\xeb\x82\x98\xeb\x8b\xa4abcd\n");
		$iterator = Iterator("\xea\xb0\x80\xeb\x82\x98\xeb\x8b\xa4abcd\n");

		$this->assertType('Iterator', $iterator);
		$this->assertType('StringIterator', $iterator);
		$this->assertSequenceEquals($expectedIterator, $iterator);
	}
}


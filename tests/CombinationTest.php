<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Combination.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';
require dirname(__FILE__) . '/../Phunctional/shortcuts.php';

final class CombinationTest extends PHPUnit_Framework_TestCase {
	function testCombine() {
		$combined = new Combination(
			new Range('a', 'g'),
			new Range(1, 7)
		);

		$this->assertEquals(
			array(
				'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5,
				'f' => 6, 'g' => 7
			),

			iterator_to_array($combined)
		);
	}

	function testCombineWhenKeyLongerThanValue() {
		$combined = new Combination(
			new Range('a', 'z'),
			new Range(1, 7)
		);

		$this->assertEquals(
			array(
				'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5,
				'f' => 6, 'g' => 7
			),

			iterator_to_array($combined)
		);
	}

	function testCombineWhenKeyShorterThanValue() {
		$combined = new Combination(
			new Range('a', 'g'),
			new Range(1, 20)
		);

		$this->assertEquals(
			array(
				'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5,
				'f' => 6, 'g' => 7
			),

			iterator_to_array($combined)
		);
	}

	function testShortcut() {
		$cases = array(
			array('keys' => xrange('a', 'g'), 'values' => xrange(1, 7)),
			array('keys' => xrange('a', 'z'), 'values' => xrange(1, 7)),
			array('keys' => xrange('a', 'g'), 'values' => xrange(1, 20))
		);

		foreach($cases as $case) {
			$this->assertEquals(
				array(
					'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5,
					'f' => 6, 'g' => 7
				),

				iterator_to_array(combine($case['keys'], $case['values']))
			);
		}
	}
}


<?php
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/iterator-utils.php';
require_once dirname(__FILE__) . '/../Phunctional/Injectable.php';
require_once dirname(__FILE__) . '/../Phunctional/Reversible.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';

final class InjectableImplementation implements IteratorAggregate, Injectable {
	function getIterator() {
		return new ArrayIterator(array());
	}

	function inject($callable) {
		return $this->injectWithInitialValue($callable, null);
	}

	function injectWithInitialValue($callable, $initial) {
		return array($callable, $initial);
	}
}

final class ReadableIterator implements IteratorAggregate {
	function __construct($array = array(), $glue = '') {
		$this->array = $array;
		$this->glue = $glue;
	}
	function getIterator() {
		return new ArrayIterator($this->array);
	}

	function __toString() {
		return join($this->glue, $this->array);
	}
}

final class ReversibleImplementation implements IteratorAggregate, Reversible {
	function getIterator() {
		return new Range(1, 5);
	}

	function reverse() {
		return new Range(5, 1);
	}
}

final class IteratorUtilitiesTest extends SequenceTestCase {
	function testAll() {
		$this->assertTrue(all(array(true, true, 1, 2, 3, 3.14, 'true', 'false')));
		$this->assertTrue(all(array(true, true, true)));
		$this->assertTrue(all(array(true)));
		$this->assertTrue(all(array(1)));
		$this->assertTrue(all(array(3.14)));
		$this->assertTrue(all(array('false')));

		$this->assertFalse(all(array(true, false, 0, 1, 2, 3.14, 'true', 'false')));
		$this->assertFalse(all(array(true, 0, 1, 2, 3.14, 'true')));
		$this->assertFalse(all(array(false, false, false)));
		$this->assertFalse(all(array(0, 0, 0)));
		$this->assertFalse(all(array(false)));
		$this->assertFalse(all(array(0)));
	}

	function testAllWithPredicate() {
		$this->assertFalse(all(array(true, true, 1, 2, 3, 3.14, 'true', 'false'), '!'));
		$this->assertFalse(all(array(true, false, 0, 1, 2, 3.14, 'true', 'false'), '!'));
		$this->assertFalse(all(array(true, 0, 1, 2, 3.14, 'true'), '!'));
		$this->assertFalse(all(array(true, true, true), '!'));
		$this->assertFalse(all(array(true), '!'));
		$this->assertFalse(all(array(1), '!'));
		$this->assertFalse(all(array(3.14), '!'));
		$this->assertFalse(all(array('false'), '!'));

		$this->assertTrue(all(array(false, false, false), '!'));
		$this->assertTrue(all(array(0, 0, 0), '!'));
		$this->assertTrue(all(array(false), '!'));
		$this->assertTrue(all(array(0), '!'));
	}

	function testAny() {
		$this->assertTrue(any(array(true, true, 1, 2, 3, 3.14, 'true', 'false')));
		$this->assertTrue(any(array(true, false, 0, 1, 2, 3.14, 'true', 'false')));
		$this->assertTrue(any(array(true, 0, 1, 2, 3.14, 'true')));
		$this->assertTrue(any(array(true, true, true)));
		$this->assertTrue(any(array(true)));
		$this->assertTrue(any(array(1)));
		$this->assertTrue(any(array(3.14)));
		$this->assertTrue(any(array('false')));

		$this->assertFalse(any(array(false, false, false)));
		$this->assertFalse(any(array(0, 0, 0)));
		$this->assertFalse(any(array(false)));
		$this->assertFalse(any(array(0)));
	}

	function testAnyWithPredicate() {
		$this->assertFalse(any(array(true, true, 1, 2, 3, 3.14, 'true', 'false'), '!'));
		$this->assertFalse(any(array(true, true, true), '!'));
		$this->assertFalse(any(array(true), '!'));
		$this->assertFalse(any(array(1), '!'));
		$this->assertFalse(any(array(3.14), '!'));
		$this->assertFalse(any(array('false'), '!'));

		$this->assertTrue(any(array(true, false, 0, 1, 2, 3.14, 'true', 'false'), '!'));
		$this->assertTrue(any(array(true, 0, 1, 2, 3.14, 'true'), '!'));
		$this->assertTrue(any(array(false, false, false), '!'));
		$this->assertTrue(any(array(0, 0, 0), '!'));
		$this->assertTrue(any(array(false), '!'));
		$this->assertTrue(any(array(0), '!'));
	}


	function testAt() {
		$arr = array('key' => 'value');
		$this->assertEquals('value', at($arr, 'key'));
		$this->assertNull(at($arr, 'non-defined-key'));
	}

	function testAtWithDefaultValue() {
		$array = array();
		$this->assertEquals(M_PI, at($array, 'non-defined-key', M_PI));
	}

	function testCompare() {
		$value = rand();
		$equals = compare($value, $value);
		$greater = compare($value, $value - 1);
		$less = compare($value - 1, $value);

		$this->assertEquals(0, $equals);
		$this->assertTrue(0 < $greater);
		$this->assertTrue(0 > $less);
	}

	function testSortingUsingCompare() {
		$array = array(1, 9, 2, 8, 3, 7, 4, 6, 5);
		usort($array, 'compare');

		$this->assertEquals(
			array(1, 2, 3, 4, 5, 6, 7, 8, 9),
			$array
		);
	}

	function testContains() {
		$array = range(1, 10);

		foreach($array as $n)
			$this->assertTrue(contains($n, $array));

		$this->assertFalse(contains(0, $array));
		$this->assertFalse(contains(11, $array));
		$this->assertFalse(contains(50, $array));
		$this->assertFalse(contains('string', $array));
	}

	function testIterableContains() {
		$range = new Range(1, 10);

		foreach($range as $n)
			$this->assertTrue(contains($n, $range));

		$this->assertFalse(contains(0, $range));
		$this->assertFalse(contains(11, $range));
		$this->assertFalse(contains(50, $range));
		$this->assertFalse(contains('string', $range));
	}

	function testCountIf() {
		$array = array(0, 'abc', 3.14, 300, array(), array(1, 2, 3), 0.7, '', '0', 1);

		$this->assertEquals(10, count_if($array, true));
		$this->assertEquals(0, count_if($array, false));
		$this->assertEquals(3, count_if($array, 'is_int'));
		$this->assertEquals(3, count_if($array, 'is_string'));
		$this->assertEquals(6, count_if($array, null));
	}

	function testDetect() {
		$array = array(0, 'abc', 3.14, 300, array(), array(1, 2, 3), 0.7, '');

		$this->assertEquals('abc', detect($array)->current());
		$this->assertEquals(0, detect($array, true)->current());
		$this->assertEquals('abc', detect($array, 'is_string')->current());
		$this->assertEquals(3.14, detect($array, 'is_float')->current());
		$this->assertEquals(array(), detect($array, 'is_array')->current());
		$this->assertNull(detect($array, false));
	}

	function testDetectElement() {
		$array = array(0, 'abc', 3.14, 300, array(), array(1, 2, 3), 0.7, '');

		$this->assertEquals('abc', detect_element($array));
		$this->assertEquals(0, detect_element($array, true));
		$this->assertEquals('abc', detect_element($array, 'is_string'));
		$this->assertEquals(3.14, detect_element($array, 'is_float'));
		$this->assertEquals(array(), detect_element($array, 'is_array'));
		$this->assertNull(detect_element($array, false));
	}

	function testDevide() {
		$divided = divide(new Range(1, 10), 3);

		$this->assertEquals($divided[1], array(3 => 4, 4 => 5, 5 => 6));
		$this->assertEquals($divided[3], array(9 => 10));
	}

	function testExceptFrom() {
		$filtered = except_from(new Range(5, 10), new Range(0, 20));

		$this->assertSequenceEquals(
			array(
				0, 1, 2, 3, 4, 11 => 11, 12 => 12,
				13 => 13, 14 => 14, 15 => 15, 16 => 16,
				17 => 17, 18 => 18, 19 => 19, 20 => 20
			),

			$filtered
		);
	}

	function testMaxElement() {
		$range = new Range(0, 50);

		$this->assertEquals(50, max_element($range));
		$this->assertEquals(50, max_element($range, null));
		$this->assertEquals(33, max_element($range, 'sin'));
		$this->assertEquals(36, max_element($range, 'tan'));
	}

	function testMaxElementOfArray() {
		$range = range(0, 50);

		$this->assertEquals(50, max_element($range));
		$this->assertEquals(50, max_element($range, null));
		$this->assertEquals(33, max_element($range, 'sin'));
		$this->assertEquals(36, max_element($range, 'tan'));
	}

	function testMaxElementOfEmptySequence() {
		$this->assertNull(max_element(''));
	}

	function testMaxElementOfEmptyArray() {
		$this->assertNull(max_element(array()));
	}

	function testMinElement() {
		$range = new Range(-25, 25);

		$this->assertEquals(-25, min_element($range));
		$this->assertEquals(-25, min_element($range, null));
		$this->assertEquals(0, min_element($range, 'abs'));
		$this->assertEquals(11, min_element($range, 'sin'));
		$this->assertEquals(-22, min_element($range, 'cos'));
	}

	function testMinElementOfArray() {
		$range = range(-25, 25);

		$this->assertEquals(-25, min_element($range));
		$this->assertEquals(-25, min_element($range, null));
		$this->assertEquals(0, min_element($range, 'abs'));
		$this->assertEquals(11, min_element($range, 'sin'));
		$this->assertEquals(-22, min_element($range, 'cos'));
	}

	function testMinElementOfEmptySequence() {
		$this->assertNull(min_element(''));
	}

	function testMinElementOfEmptyArray() {
		$this->assertNull(min_element(array()));
	}

	function testmerge() {
		$merged = merge(new range(1, 5), range(6, 10));
		$this->assertEquals(range(1, 10), $merged);
	}

	function testmergeMany() {
		$merged = merge(
			new range(1, 5), range(6, 10),
			new Range(11, 15), range(16, 20)
		);

		$this->assertEquals(range(1, 20), $merged);
	}

	static function callbackForReduce_plus($a, $b) { return $a + $b; }

	function testReduce() {
		$reduced = reduce(
			array(__CLASS__, 'callbackForReduce_plus'),
			new Range(1, 50)
		);

		$this->assertEquals(array_sum(range(1, 50)), $reduced);
	}

	function testReduceWithInitial() {
		$reduced = reduce(
			array(__CLASS__, 'callbackForReduce_plus'),
			new Range(1, 50),
			1.234
		);

		$this->assertEquals(array_sum(range(1, 50)) + 1.234, $reduced);
	}

	function testReduceInjectable() {
		$reduced = reduce(null, new InjectableImplementation);
		$expected = array(Functor(null), null);

		$this->assertEquals($expected, $reduced);
	}

	function testReduceInjectableWithInitialValue() {
		$reduced = reduce(null, new InjectableImplementation, 'initial');
		$expected = array(Functor(null), 'initial');

		$this->assertEquals($expected, $reduced);
	}

	function testSum() {
		$this->assertEquals(55, sum(range(1, 10)));
		$this->assertEquals(55, sum(new Range(1, 10)));
		$this->assertEquals(30, sum(range(-5, 5), 'abs'));
		$this->assertEquals(30, sum(new Range(-5, 5), 'abs'));
	}

	function testProduct() {
		$this->assertEquals(3628800, product(range(1, 10)));
		$this->assertEquals(3628800, product(new Range(1, 10)));
		$this->assertEquals(0, product(range(-5, 5), 'abs'));
		$this->assertEquals(0, product(new Range(-5, 5), 'abs'));
		$this->assertEquals(3628800, product(range(-10, -1), 'abs'));
		$this->assertEquals(3628800, product(new Range(-10, -1), 'abs'));
	}

	function testGroupBy() {
		$this->assertEquals(
			array(1 => array(1, -1), 2 => array(2, -2), 3 => array(3, -3)),
			groupby(array(1, 2, 3, -1, -2, -3), 'abs')
		);

		$this->assertEquals(
			array('a' => array('a', 'a'), 'b' => array('b'), 'c' => array('c')),
			groupby('abca')
		);
	}

	function testSlice() {
		$this->beginStringTest();

		$this->assertSequenceEquals(range(5, 20), slice(new Range(5, 20)));
		$this->assertEquals(range(5, 20), slice(range(5, 20)));
		$this->assertEquals('test string', slice('test string'));

		$this->assertSequenceEquals(
			array(3 => 8, 9, 10), slice(new Range(5, 10), 3)
		);

		$this->assertEquals(array(3 => 8, 9, 10), slice(range(5, 10), 3));
		$this->assertEquals('자열', slice('test 문자열', 6));

		$this->assertSequenceEquals(
			array(3 => 8, 9, 10),
			slice(new Range(5, 10), 3, 5)
		);

		$this->assertEquals(array(3 => 8, 9, 10), slice(range(5, 10), 3, 5));
		$this->assertEquals('t 문자', slice('test 문자열', 3, 4));

		$this->finishStringTest();

		foreach(array(range(5, 20), new Range(5, 20)) as $seq) {
			$this->assertSequenceEquals(
				array(3 => 8, 9, 10, 11, 12, 13, 14),
				slice($seq, 3, 7)
			);

			foreach(range(-1, -5) as $unexpectedValue) {
				try {
					slice($seq, 3, $unexpectedValue);
					$this->fail();
					break 2;
				}
				catch(UnexpectedValueException $e) {
					continue;
				}
			}
		}
	}

	function testArrayToArray() {
		$expected = range(1, 10);
		$this->assertEquals($expected, to_array($expected));
	}

	function testIteratorToArray() {
		$expected = range(1, 10);
		$iterator = new ArrayIterator($expected);

		$this->assertEquals($expected, to_array($iterator));
	}

	function testIteratorAggregateToArray() {
		$expected = range(1, 10);
		$iterAggr = new ArrayObject($expected);

		$this->assertEquals($expected, to_array($iterAggr));
	}

	function testMixedToString() {
		$this->assertEquals('1', to_str(1));
		$this->assertEquals('1.123', to_str(1.123));
		$this->assertEquals('1', to_str(true));
		$this->assertEquals('', to_str(false));
		$this->assertEquals('', to_str(null));
		$this->assertEquals('abc', to_str('abc'));
		$this->assertEquals('가나다', to_str('가나다'));
		$this->assertEquals('123', to_str(array(1, 2, 3)));
		$this->assertEquals('헬로키티', to_str(Iterator("헬로키티")));
		$this->assertEquals(
			'1-2-10-a-b-c',
			to_str(new ReadableIterator(array(1, 2, 10, 'a', 'b', 'c'), '-'))
		);

		$this->assertEquals(
			'a@s@d@f@g',
			to_str('@', array('a', 's', 'd', 'f', 'g'))
		);

		$this->assertEquals(
			'Hello, world',
			to_str(', ', new ArrayIterator(array('Hello', 'world')))
		);

		$this->assertEquals(
			'Heung-sub-Lee',
			to_str('-', new ReadableIterator(array('Heung', 'sub', 'Lee')))
		);

		$this->beginStringTest();
		$this->assertEquals('말..미..잘', to_str('..', "말미잘"));
		$this->assertEquals('새0우0깡', to_str(0, "새우깡"));
		$this->assertEquals('새0우0깡', to_str(0, "새우깡", 1, 2));
		$this->finishStringTest();

		$this->assertEquals(
			'Where_Yo_Is_Yo_Ma_Yo_Way',
			to_str(array('_', 'Yo', '_'), array('Where', 'Is', 'Ma', 'Way'))
		);
	}

	function testSorted() {
		$this->assertSequenceEqualsWithIndex(
			range(10, 1),
			sorted(new Range(10, 1), '($0 > $1 ? -1 : 1)')
		);

		$this->assertSequenceEqualsWithIndex(
			array(9=>10, 8=>9, 7=>8, 6=>7, 5=>6, 4=>5, 3=>4, 2=>3, 1=>2, 0=>1),
			sorted(new Range(1, 10), '($0 > $1 ? -1 : 1)')
		);

		$this->assertSequenceEqualsWithIndex(
			array(5 => 'a', 4 => 'b', 3 => 'c', 2 => 'd', 1 => 'e', 0 => 'f'),
			sorted(new Range('f', 'a'), 'strcmp')
		);

		$this->assertSequenceEqualsWithIndex(
			array(5 => 'f', 4 => 'e', 3 => 'd', 2 => 'c', 1 => 'b', 0 => 'a'),
			sorted(new Range('a', 'f'), '(strcmp($1, $0))')
		);
	}

	function testSortedWithoutComparer() {
		$this->assertSequenceEqualsWithIndex(
			array(9=>1, 8=>2, 7=>3, 6=>4, 5=>5, 4=>6, 3=>7, 2=>8, 1=>9, 0=>10),
			sorted(new Range(10, 1))
		 );

		$this->assertSequenceEqualsWithIndex(
			array(5=>'a', 4=>'b', 3=>'c', 2=>'d', 1=>'e', 0=>'f'),
			sorted(new Range('f', 'a'))
		);
	}

	function testSortedByKey() {
		$this->assertEquals(
			array(1 => 'd', 2 => 'ef', 0 => 'abc', 3 => 'ghijk'),
			sorted_by_key(array('abc', 'd', 'ef', 'ghijk'), 'strlen')
		);

		$this->assertEquals(
			array(3 => 'ghijk', 0 => 'abc', 2 => 'ef', 1 => 'd'),
			sorted_by_key(array('abc', 'd', 'ef', 'ghijk'), 'strlen', true)
		);
	}

	function testReversed() {
		$this->assertSequenceEqualsWithIndex(
			array(5 => 10, 4 => 9, 3 => 8, 2 => 7, 1 => 6, 0 => 5),
			reversed(range(5, 10))
		);

		$this->beginStringTest();
		$this->assertEquals('cba 다나가', reversed('가나다 abc'));
		$this->finishStringTest();

		$this->assertSequenceEquals(
			range(5, 1),
			reversed(new ReversibleImplementation)
		);

		$this->assertSequenceEquals(
			array('5', '4', '3', '2', '1'),
			reversed(map('strval', range(1, 5)))
		);
	}
}

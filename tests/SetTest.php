<?php
require_once dirname(__FILE__) . '/SequenceTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Set.php';
require_once dirname(__FILE__) . '/../Phunctional/Range.php';
require dirname(__FILE__) . '/../Phunctional/shortcuts.php';

final class SetTest extends SequenceTestCase {
	function testInitialize() {
		$this->assertSequenceEquals(array(), new Set);
		$this->assertSequenceEquals(range(1, 5), new Set(range(1, 5)));
		$this->assertSequenceEquals(range(1, 5), new Set(new Range(1, 5)));
		$this->assertSequenceEquals(array(1, 2), new Set(array(1, 2, 1, 2)));
	}

	function testAdd() {
		$set = new Set;

		$set->add(1);
		$this->assertSequenceEquals(array(1), $set);

		$set->add(1);
		$this->assertSequenceEquals(array(1), $set);

		$set->add(2);
		$this->assertSequenceEquals(array(1, 2), $set);

		$set->add(2);
		$this->assertSequenceEquals(array(1, 2), $set);

		$set->add(3);
		$this->assertSequenceEquals(array(1, 2, 3), $set);

		$set->add(3);
		$this->assertSequenceEquals(array(1, 2, 3), $set);

		$set->add(3, 4, 5);
		$this->assertSequenceEquals(range(1, 5), $set);
	}

	function testAddWithVariousTypes() {
		$set = new Set;

		$set->add(1);
		$set->add('1');
		$set->add(1.0);

		$this->assertSequenceEquals(array(1, '1', 1.0), $set);
	}

	function testAppend() {
		$set = new Set;

		$this->assertType('Set', $set->append(1));
		$this->assertSequenceEquals(array(1), $set->append(1));
		$this->assertSequenceEquals(array(1), $set->append(1)->append(1));
		$this->assertSequenceEquals(array(1, 2), $set->append(1)->append(2));
		$this->assertSequenceEquals(array(), $set);

		$this->assertSequenceEquals(
			array(1, 2),
			$set->append(1)->append(1)->append(2)
		);

		$this->assertSequenceEquals(
			array(1, 2),
			$set->append(1)->append(2)->append(2)
		);

		$this->assertSequenceEquals(
			array(1, 2),
			$set->append(1)->append(1)->append(2)->append(2)
		);

		$this->assertSequenceEquals(array(1, 2), $set->append(1, 2));
		$this->assertSequenceEquals(array(1, 2), $set->append(1, 1, 2));
		$this->assertSequenceEquals(array(1, 2), $set->append(1, 2, 2));
		$this->assertSequenceEquals(array(1, 2), $set->append(1, 1, 2, 2));
	}

	function testMerge() {
		$set = new Set(new Range(1, 5));

		$set->merge(range(6, 10));
		$this->assertSequenceEquals(range(1, 10), $set);

		$set->merge(range(5, 15));
		$this->assertSequenceEquals(range(1, 15), $set);
	}

	function testUnion() {
		$set = new Set(new Range(1, 5));
		$this->assertSequenceEquals(range(1, 10), $set->union(range(6, 10)));
		$this->assertSequenceEquals(range(1, 10), $set->union(range(1, 10)));
	}

	function testIntersect() {
		$a = new Set(new Range(1, 10));
		$b = new Set(new Range(5, 15));

		$this->assertSequenceEquals(range(5, 10), $a->intersect($b));
		$this->assertSequenceEquals(range(5, 10), $b->intersect($a));
		$this->assertSequenceEquals(range(5, 10), $a->intersect(range(5, 15)));
		$this->assertSequenceEquals(range(5, 10), $b->intersect(range(1, 10)));

		$this->assertSequenceEquals(
			range(5, 10),
			$b->intersect(array_merge(range(1, 10), range(5, 10)))
		);
	}

	function testIntersectWithVariousTypes() {
		$set = new Set(array(1, '1', 1.0, '1.0'));

		$this->assertSequenceEquals(
			array('1', 1.0),
			$set->intersect(array('1', 1.0, '1.00'))
		);
	}

	function testRemove() {
		$set = new Set(range(1, 5));

		$set->remove(6);
		$this->assertEquals(5, count($set));

		$set->remove(2);
		$this->assertEquals(4, count($set));

		$set->remove(2);
		$this->assertEquals(4, count($set));
	}

	function testExcept() {
		$set = new Set(range(1, 5));

		$set->except(1);
		$this->assertEquals(5, count($set));

		$this->assertEquals(5, count($set->except(6)));
		$this->assertEquals(4, count($set->except(3)));
		$this->assertEquals(4, count($set->except(3)->except(3)));
	}

	function testComplement() {
		$set = new set(range(1, 10));

		$this->assertSequenceEquals(
			range(1, 3),
			$set->complement(range(4, 15))
		);

		$this->assertSequenceEquals(
			array(1, 2, 3, 9, 10),
			$set->complement(new Range(4, 8))
		);
	}

	function testContains() {
		$set = new Set(array(1, 1, 2, 1, 2));

		$this->assertTrue($set->contains(1));
		$this->assertTrue($set->contains(2));
		$this->assertTrue($set->contains(1, 2));
		$this->assertFalse($set->contains(0));
		$this->assertFalse($set->contains(3));
		$this->assertFalse($set->contains(1, 2, 3));
	}

	function testContainsAll() {
		$set = new Set(array(1, 1, 2, 1, 2));

		$this->assertTrue($set->containsAll(array(1)));
		$this->assertTrue($set->containsAll(array(2)));
		$this->assertTrue($set->containsAll(array(1, 2)));
		$this->assertFalse($set->containsAll(array(3)));
		$this->assertFalse($set->containsAll(array(1, 2, 3)));
	}

	function testContainsAny() {
		$set = new Set(array(1, 1, 2, 1, 2));

		$this->assertTrue($set->containsAny(array(1)));
		$this->assertTrue($set->containsAny(array(2)));
		$this->assertTrue($set->containsAny(array(1, 2)));
		$this->assertTrue($set->containsAny(array(1, 2, 3)));
		$this->assertFalse($set->containsAny(array(3)));
		$this->assertFalse($set->containsAny(array(3, 4)));
	}

	function testToArray() {
		$set = new Set(array(1, 2));
		$this->assertEquals(array(1, 2), $set->toArray());

		$set = new Set(array(1, 2, 1, 2));
		$this->assertEquals(array(1, 2), $set->toArray());
	}

	function testShortcut() {
		$this->assertEquals(new Set(xrange(1, 3)), Set(1, 2, 3, 3, 2, 1));
	}

	function testCardinality() {
		$sets = array(
			10 => new Range(1, 10),
			3 => array(1, 1, 2, 2, 3, 3),
			5 => range(3, 7),
			8 => 'hello world'
		);

		foreach($sets as $card => $set) {
			$set = new Set($set);
			$this->assertEquals($card, $set->cardinality);
			$this->assertEquals($set->cardinality, count($set));
		}
	}
}


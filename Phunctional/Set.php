<?php
/*
Title: Set
	중복값을 갖지 않는 집합(set) 자료 구조.

Dependencies:
	- <Iterator>
*/
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Class: Set
		중복값을 갖지 않는 집합(set) 자료 구조. *저장되는 값들은 순서를 보장할 수 없다.* 값의 비교에는 == 연산자 대신 === 연산자가 사용된다.

	Implements:
		- IteratorAggregate
		- Countable

	See Also:
		- <http://www.php.net/manual/en/function.array-unique.php>
*/
final class Set implements IteratorAggregate, Countable {
	protected $values = array();

	/*
		Property: $cardinality
			(integer) 기수. 집합에 포함된 원소의 수.
	*/
	public $cardinality;

	/*
		Constructor: __construct()
			집합을 생성한다. 복사할 순차열을 받을 수 있다. 복사할 순차열에 중복 값이 있어도 합쳐진다.

			(start code)
			assert(0 == count(new Set));
			assert(5 == count(new Set(array(1, 2, 3, 4, 5, 1, 2, 3, 4, 5))));
			(end)

		Parameters:
			(iterable $iterable) - 복사할 순차열. 없을 경우 빈 집합이 된다.
	*/
	function __construct($iterable = array()) {
		foreach(Iterator($iterable) as $value)
			$this->add($value);
		$this->cardinality = count($this->values);
	}

	/*
		Method: contains()
			집합이 값을 포함하는지 확인한다. 둘 이상의 인수를 전달할 경우, 값들 모두가 포함되는지 확인한다.

			(start code)
			$a = new set(range(1, 5));

			assert($a->contains(1));
			assert($a->contains(1, 2));
			assert($a->contains(1, 2, 3));

			assert(!$a->contains(0));
			assert(!$a->contains(0, 1));
			assert(!$a->contains(0, 5));
			(end)

		Parameters:
			$value... - 포함되는지 확인할 값(들)

		Returns:
			(boolean) 모두 포함할 경우 true.
	*/
	function contains($value) {
		return $this->containsAll(func_get_args());
	}

	/*
		Method: containsAll()
			인수로 받은 순차열이 집합의 부분 집합(subset)인지 확인한다.

			(start code)
			$a = new set(range(1, 5));

			assert($a->containsAll((array) 1));
			assert($a->containsAll(array(1, 2)));
			assert($a->containsAll(new Range(1, 5)));

			assert(!$a->containsAll((array) 0));
			assert(!$a->containsAll(array(0, 1)));
			assert(!$a->containsAll(array(0, 5)));
			(end)

		Parameters:
			iterable $values - 부분 집합인지 확인할 순차열.

		Returns:
			(boolean) $values가 부분 집합일 경우 true.
	*/
	function containsAll($values) {
		foreach(Iterator($values) as $value) {
			if(!in_array($value, $this->values, true))
				return false;
		}

		return true;
	}

	/*
		Method: containsAny()
			인수로 받은 순차열이 집합의 교집합(intersection)인지 확인한다.

			(start code)
			$set = new Set(array(1, 1, 2, 1, 2));

			assert($set->containsAny((array) 1));
			assert($set->containsAny((array) 2));
			assert($set->containsAny(array(1, 2)));
			assert($set->containsAny(new Range(1, 3)));
			assert(!$set->containsAny((array) 3));
			assert(!$set->containsAny(array(3, 4)));
			(end)

		Parameters:
			iterable $values - 교집합인지 확인할 순차열.

		Returns:
			(boolean) $values가 교집합일 경우 true.
	*/
	function containsAny($values) {
		foreach(Iterator($values) as $value) {
			if(in_array($value, $this->values, true))
				return true;
		}

		return false;
	}

	/*
		Method: add()
			집합에 값을 추가한다. 이미 있는 값은 추가하지 않는다.

			(start code)
			$set = new Set;

			$set->add(1);
			$set->add('1');
			$set->add(1.0);
			$set->add(1);

			assert(3 == count($set));
			(end)

		Parameters:
			mixed $value - 추가할 값.

		See Also:
			- <Set->append()>
	*/
	function add($value) {
		foreach(func_get_args() as $value) {
			if(!in_array($value, $this->values, true)) {
				$this->values[] = $value;
				++$this->cardinality;
			}
		}
	}

	/*
		Method: append()
			집합에 값이 추가된 새로운 집합을 반환한다. <Set->add()> 메서드와 달리 집합 객체 스스로에 변경을 가하지 않는다. 

			(start code)
			$set = new Set;

			$set->append(1);
			assert(0 == count($set));

			assert(1 == count($set->append('1')));
			assert(2 == count($set->append('1')->append(1)));
			assert(1 == count($set->append(1)->append(1)));
			(end)

		Parameters:
			mixed $value - 추가할 값.

		Returns:
			(<Set>) 값이 더해진 새로운 집합.
	*/
	function append($value) {
		$set = clone $this;
		foreach(func_get_args() as $value)
			$set->add($value);
		return $set;
	}

	/*
		Method: merge()
			전달된 순차열의 모든 원소를 집합에 포함시킨다.
			원소는 중복되지 않는다.

			(start code)
				$set = new Set(new Range(1, 5));

				$set->merge(range(6, 10));
				assert(range(1, 10), $set);

				$set->merge(range(5, 15));
				assert(range(1, 15), $set);
			(end)

		Parameters:
			iterable $values - 합할 순차열.
	*/
	function merge($iterable) {
		foreach(Iterator($iterable) as $value)
			$this->add($value);
	}

	/*
		Method: union()
			전달된 순차열과의 합집합(union)을 구한다.

			(start code)
			$set = new Set(new Range(1, 5));
			assert(range(1, 10) == $set->union(range(6, 10))->toArray());
			assett(range(1, 10) == $set->union(range(1, 10))->toArray());
			(end)

		Parameters:
			iterable $values - 합할 순차열.

		Returns:
			(<Set>) 합집합.
	*/
	function union($iterable) {
		$set = clone $this;
		$set->merge($iterable);
		return $set;
	}

	/*
		Method: intersect()
			전달된 순차열과의 교집합(intersection)을 구한다.

			(start code)
			$a = new Set(range(1, 10));
			assert(range(5, 10) == $a->intersect(range(5, 15))->toArray());
			assert(range(5, 10) == $a->intersect(new Range(5, 15))->toArray());
			assert(range(5, 10) == $a->intersect(new Set(range(5, 15)))->toArray());
			(end)

		Parameters:
			iterable $values - 교차할 순차열.

		Returns:
			(<Set>) 교집합.
			
	*/
	function intersect($iterable) {
		$set = array();
		foreach(Iterator($iterable) as $value) if($this->contains($value))
			$set[] = $value;
		return new Set($set);
	}

	/*
		Method: remove()
			집합에 있는 값을 뺀다. 없는 값이면 무시한다.

			(start code)
			$set = new Set(range(1, 5));

			$set->remove(6);
			assert(5 == count($set));

			$set->remove(2);
			assert(4 == count($set));

			$set->remove(2);
			assert(4 == count($set));
			(end)

		Parameters:
			mixed $value - 집합에서 제거할 값.
	*/
	function remove($value) {
		if(false === $i = array_search($value, $this->values, true))
			return;

		$this->values = array_merge(
			array_slice($this->values, 0, $i),
			array_slice($this->values, $i + 1, count($this->values) - $i - 1)
		);

		$this->cardinality = count($this->values);
	}

	/*
		Method: except()
			집합에서 특정 값만 제외한 부분 집합을 반환한다. 집합에서 해당 값을 찾을 수 없으면, 복제된 동등한 집합을 반환한다. <Set->remove()> 메서드와 달리 집합 객체 스스로에 변경을 가하지 않는다.

			(start code)
			$set = new Set(range(1, 5));

			$set->except(1);
			assert(5 == count($set));

			assert(5 == count($set->except(6)));
			assert(4 == count($set->except(3)));
			assert(4 == count($set->except(3)->except(3)));
			(end)

		Parameters:
			mixed $value - 집합에서 제외할 값.

		Returns:
			(<Set>) $value를 제외한 새로운 부분 집합.
	*/
	function except($value) {
		$set = clone $this;

		if(false === $i = array_search($value, $this->values, true))
			return $set;

		$set->values = array_merge(
			array_slice($this->values, 0, $i),
			array_slice($this->values, $i + 1, count($this->values) - $i - 1)
		);

		$set->cardinality = count($set->values);

		return $set;
	}

	/*
		Method: complement()
			전달된 순차열과의 차집합을 구한다.

			(start code)
			$set = new set(range(1, 10));
			assert(range(1, 3) == $set->complement(range(4, 15))->toArray());
			(end)

		Parameters:
			iterable $iterable - 제외할 원소를 포함한 순차열.

		Returns:
			(<Set>) 차집합.
	*/
	function complement($iterable) {
		$set = clone $this;
		foreach(Iterator($iterable) as $value)
			$set->remove($value);
		return $set;
	}

	function getIterator() {
		return new ArrayIterator($this->values);
	}

	/*
		Method: count()
			포함한 원소의 수(기수)를 반환한다. 중복된 값들은 하나로 센다. count() 함수에 인스턴스를 넣을 수 있다.

			(start code)
			assert(0 == count(new Set));
			assert(1 == count(new Set((array) 'is_int')));
			assert(2 == count(new Set(array('is_int', 'is_string'))));
			(end)

		Returns:
			(int) 들어있는 원소의 수(기수).
	*/
	function count() {
		return $this->cardinality;
	}

	/*
		Method: toArray()
			들어있는 값들의 배열을 반환한다. **순서는 의미가 없다.**

		Returns:
			(array) 들어있는 값들의 배열.
	*/
	function toArray() {
		return $this->values;
	}
}

function Set() {
	return new Set(func_get_args());
}

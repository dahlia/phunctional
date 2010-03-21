<?php
/*
Title: FunctionArray
	함수 객체만 취급하는 배열.

Dependencies:
	- <Functor>
	- <Transformation>
*/
require_once dirname(__FILE__) . '/Functor.php';
require_once dirname(__FILE__) . '/Transformation.php';

/*
	Class: FunctionArray
		일반 배열—정확히는 ArrayObject 인스턴스—과 동일하게 작동하지만, 모든 값들을 함수 객체로 취급한다. 모든 추가되는 값들은 내부적으로 <Functor()> 함수를 통과한 객체를 저장하게 된다.

		(start code)
		$array = new FunctorArray;
		$array[] = 'is_int';

		assert(new Callback('is_int') == $array[0]);
		(end)

	Extends:
		ArrayObject

	Implements:
		- IteratorAggregate
		- ArrayAccess
		- Countable

	See Also:
		- <EventHandler>
*/
final class FunctionArray extends ArrayObject {
	/*
		Constructor: __construct()
			배열을 생성한다. 복사할 순차열을 인수로 넘길 수 있다. 복사되는 순차열의 원소들은 모두 함수자(functor)로 취급된다.

			(start code)
			assert(0 == count(new FunctionArray));
			assert(5 == count(new FunctionArray(range(1, 5))));
			assert(5 == count(new FunctionArray(new Range(1, 5))));
			(end)

		Parameters:
			(iterable $iterable) - 초기화할 순차열. 없으면 빈 배열이 된다.
	*/
	function __construct($iterable = array()) {
		parent::__construct(iterator_to_array(
			new Transformation('Functor', $iterable)
		));
	}

	function offsetSet($key, $functor) {
		parent::offsetSet($key, Functor($functor));
	}

	function append($functor) {
		parent::append(Functor($functor));
	}
}


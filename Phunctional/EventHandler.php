<?php
/*
Title: EventHandler
	하나 이상의 콜백을 등록할 수 있는 이벤트 핸들러.

Dependencies:
	- <Functor>
	- <Iterator>
*/
require_once dirname(__FILE__) . '/Functor.php';
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Class: EventHandler
		여러 함수자들을 담는 순차열 함수자. 순차열이기 때문에 iterable하며 functor이기 때문에 호출이 가능하다. 호출할 경우 순차열이 포함한 원소(함수자)들을 순서대로 호출하고, 그것들이 반환한 값들의 배열을 반환한다. 특정 이벤트 핸들러에 대해 서로 다른 여러 모듈로부터 콜백을 등록받기 위해 사용한다.

		(start code)
		$eventHandler = new EventHandler;

		$eventHandler[] = 'is_int';
		$eventHandler[] = 'is_string';
		$eventHandler[] = new PartialApplication(
			'substr', array(1 => 1, 2 => -1));

		assert($eventHandler[0] instanceof Callback);
		assert($eventHandler[2] instanceof PartialApplication);

		$result = $eventHandler->call('test string');
		assert($result[0] == false);
		assert($result[1] == true);
		assert($result[2] == 'est strin');
		(end)

	Extends:
		<Functor>

	Implements:
		- ArrayAccess
		- <Callable>
		- Countable
		- IteratorAggregate

	See Also:
		- <FunctionArray>
*/
class EventHandler extends Functor implements ArrayAccess, Countable, IteratorAggregate {
	protected $functors = array();

	/*
		Method: apply()
			포함한 함수자들을 모두 호출한다. 원소 $f 각각에 대해 $f->apply($parameters)를 호출한다.

		Parameters:
			array $parameters - 함수자들에게 전달한 인자들

		Returns:
			(array) 호출된 함수자들이 반환한 값들을 순서대로 담은 배열.
	*/
	function apply(array $parameters) {
		$results = array();
		foreach($this->functors as $functor)
			$results[] = $functor->apply($parameters);
		return $results;
	}

	/*
		Method: offsetGet()
			오프셋 $i에 위치한 함수자를 반환한다.

			(start code)
			assert(count($eventHandler) == 1);
			assert($eventHandler[0] instanceof Callable);
			assert($eventHandler[0] == $eventHandler->offsetGet(0));
			(end)

		Parameters:
			int $i - 오프셋. 음수일 경우 오프셋을 count($this) + $i로 본다.

		Returns:
			(Callable|null) 해당 오프셋에 위치한 함수자. 없을 경우 null.
	*/
	function offsetGet($i) {
		$length = $this->count();

		if($i < 0)
			$i += $length;

		return ($length > $i and $i >= 0) ? $this->functors[$i] : null;
	}

	/*
		Method: offsetSet()
			원소를 추가한다. *이미 추가되어 있는 자리에 다른 함수자를 넣는 것은 불가능하다.* 배열에 원소를 추가하듯이 사용한다.

			(start code)
			$eventHandler[] = 'callback';
			(end)
	*/
	function offsetSet($i, $functor) {
		if(!is_null($i))
			throw new InvalidArgumentException('Unexpected key');

		$this->functors[] = $functor = Functor($functor);
		return $functor;
	}

	function offsetUnset($i) {
		throw new BadMethodCallException();
	}

	/*
		Method: offsetExists()
			(start code)
			assert(isset($eventHandler[0]) == $eventHandler->offsetExists(0));
			(end)

		Parameters:
			int $i - 함수자가 있는지 확인할 오프셋. 음수일 경우 count($this) + $i번째 함수자가 있는지 확인하게 된다.

		Returns:
			(bool) 오프셋 $i에 함수자가 있으면 true, 없으면 false.
	*/
	function offsetExists($i) {
		return $i < 0 ? abs($i) <= $this->count() : $i < $this->count();
	}

	/*
		Method: count()
			이벤트 핸들러에 등록된 함수자의 갯수를 반환한다. count() 함수를 위해서 존재한다.

			(start code)
			assert(count($eventHandler) == iterator_count($eventHandler));
			assert(count($eventHandler) == $eventHandler->count());
			(end)

		Returns:
			(int) 이벤트 핸들러에 등록된 함수자의 갯수.

		See Also:
			Countable
	*/
	function count() {
		return count($this->functors);
	}

	/*
		Method: getIterator()
			포함하는 함수자 원소들에 대한 반복자를 반환한다.

		Returns:
			(Iterator) 포함한 함수자들에 대한 반복자.

		See Also:
			IteratorAggregate
	*/
	function getIterator() {
		return Iterator($this->functors);
	}
}

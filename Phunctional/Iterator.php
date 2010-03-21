<?php
/*
Title: Iterator
	배열(array)이나 IteratorAggregate 인스턴스 등 다양한 순차열 형식들을 일관적인 반복자(Iterator) 인터페이스로 사용할 수 있게 하는 <Iterator()> 함수를 제공한다.

Dependencies:
	- <Functor>
	- <StringIterator>

See Also:
	<http://www.php.net/~helly/>
*/
require_once dirname(__FILE__) . '/Functor.php';

/*
	Function: Iterator()
		어떤 값이나 객체든지 반복자(Iterator) 인스턴스로 반환해준다.

	Parameters:
		array|Traversable $iterable - 반복자를 얻을 값.

	Returns:
		$iterable에 대한 Iterator 인스턴스.

		- Iterator 인스턴스일 경우 그대로
		- 배열(array)일 경우 ArrayIterator 인스턴스로
		- 문자열(string)일 경우 <StringIterator> 인스턴스로
		- IteratorAggregate 인스턴스일 경우 <IteratorAggregateIterator> 인스턴스로

		반환하며, 그 외의 경우에는 InvalidArgumentException 예외를 던진다.

	Pseudo Types:
		iterable - 이 <Iterator()> 함수가 수용할 수 있는 값이나 인스턴스 객체. 배열(array), 문자열(string) 혹은 Iterator 인스턴스 혹은 IteratorAggregate 인스턴스.

		(start code)
		iterable := array | string | Traversable
		Traversable := Iterator | IteratorAggregate
		(end)

	See Also:
		<http://www.php.net/manual/en/language.oop5.iterations.php>
*/
function Iterator($iterable, $registerCallback = false) {
	static $callbacks = array();

	if($registerCallback) {
		$callbacks[] = Functor($iterable);
		return;
	}

	foreach($callbacks as $callback) {
		try {
			$object = $callback->call($iterable);
			break;
		}
		catch(InvalidArgumentException $e) {
			continue;
		}
	}

	if(is_array($iterable))
		$iterable = new ArrayIterator($iterable);
	else if(is_string($iterable)) {
		require_once dirname(__FILE__) . '/StringIterator.php';
		$iterable = new StringIterator($iterable);
	}
	else if($iterable instanceof IteratorAggregate)
		$iterable = new IteratorAggregateIterator($iterable);

	if($iterable instanceof Iterator)
		return $iterable;

	throw new InvalidArgumentException('Passed variable is not array or instance of Traversable');
}

/*
	Class: IteratorAggregateIterator
		IteratorAggregate에 대한 반복자 클래스. 지연 평가를 위해 사용한다. IteratorAggregate->getIterator()가 고비용 연산을 할 경우가 있기 때문에, 실제로 반복을 요하기 전까지는 IteratorAggregate->getIterator()를 호출하지 않는다.
*/
final class IteratorAggregateIterator implements Iterator {
	public $iteratorAggregate;
	protected $iterator = null;

	/*
		Constructor: __construct()

		Parameters:
			IteratorAggregate $iteratorAggregate - 반복자로 만들 IteratorAggregate 인스턴스.
	*/
	function __construct(IteratorAggregate $iteratorAggregate) {
		$this->iteratorAggregate = $iteratorAggregate;
	}

	protected function getIterator() {
		if(is_null($this->iterator))
			$this->iterator = $this->iteratorAggregate->getIterator();
		return $this->iterator;
	}

	function valid() {
		return $this->getIterator()->valid();
	}

	function next() {
		return $this->getIterator()->next();
	}

	function current() {
		return $this->getIterator()->current();
	}

	function key() {
		return $this->getIterator()->key();
	}

	function rewind() {
		return $this->getIterator()->rewind();
	}
}

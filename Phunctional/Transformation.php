<?php
/*
Title: Transformation
	배열 등의 순차열에서 원소 각각에 특정 함수를 적용한 또다른 순차열를 구할 때 사용한다.

Dependencies:
	- <Functor>
	- <Iterator>
	- <Mappable>

See Also:
	- <http://en.wikipedia.org/wiki/Map_(higher-order_function)>
	- <http://www.sgi.com/tech/stl/transform.html>
	- <http://docs.python.org/lib/built-in-funcs.html#l2h-49>
	- <http://www.ruby-doc.org/core/classes/Enumerable.html#M003159>
	- <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:map>
*/
require_once dirname(__FILE__) . '/Functor.php';
require_once dirname(__FILE__) . '/Iterator.php';
require_once dirname(__FILE__) . '/Mappable.php';

/*
	Class: Transformation
		Transformation 클래스는 배열 등 반복 가능한 순차열(즉, 반복자를 구현한 객체)에서 원소 각각에 특정 함수를 적용한 또다른 순차열를 구할 때 사용한다. Transformation 자체도 Iterator의 일종이므로, Transformation 인스턴스를 Transformation하는 것도 가능하다. (그러나 그렇게 하는 대신 합성 함수(<CompositeFunction>)를 사용하는 편이 낫다.)

		Transformation이 array_map과 다른 두 가지가 있다. (<Filter> 와 마찬가지로.)

		- 표준 콜백 형식 외에도 Phunctional의 <Callable> 인스턴스도 허용한다는 것
		- 그 자신이 반복자(Iterator)의 일종이고, next() 메서드가 호출되기 전까지는 아무런 행동도 하지 않는다는 것—게으른 연산(lazy evaluation)

		두번째 특징은 일반적으로 좀 더 효율적인 성능을 내는 데에 도움을 준다. 하지만, 같은 연산(특정 원소를 거르는 것)을 연속적으로 해야한다면, iterator_to_array() 같은 함수를 이용해서 배열로 만들어놓고 그 배열을 계속 사용하는 것이 성능상 더 좋다. 같은 결과를 반환하는 연산을 무식하게 반복적으로 하기 때문이다.

		아래 코드는 $words 배열에 든 문자열 각각의 길이를 구하는 예제.

		(start code)
		$words = explode(' ', "The best way to predict the future is to invent it.");
		$word_lengths = new Transformation('strlen', $words);
		(end)

		위 코드에서 유의할 점은 $word_lengths는 배열이 아니라 반복자(Iterator)의 일종인 Transformation 인스턴스라는 점이다. 즉, 저 시점에서는 우리가 의도하는 아무런 연산도 일어나지 않는다. 연산이 일어나는 것은 실제로 반복자가 작용할 때이다. 반복자는 foreach문이나 next() 메서드를 호출할 때, 혹은 <Filter>, Transformation과 같이 내부적으로 next() 메서드를 호출하는 코드에서 작용한다.

		(start code)
		foreach($word_lengths as $length)
			echo "$length, ";
		(end)

		위 코드의 foreach 부분에 이르러서야 $word_lengths는 연산을 하게 된다. 위 코드는 아래와 같은 결과를 출력한다.

		(start code)
		3, 4, 3, 2, 7, 3, 6, 2, 2, 6, 3, 
		(end)

	Implements:
		- Iterator
*/
class Transformation implements Iterator {
	public $functor;
	public $iterator;
	public $mappable;

	/*
		Constructor: __construct()

		Parameters:
			functor $functor - 원소에 적용할 함수자. 인자로 원소와 키가 전달된다.
			iterable|Mappable $iterable - 걸러낼 순차열. <Mappable> 인스턴스일 경우 생성된 <Transformation> 인스턴스는 $iterable->map($functor)가 반환하는 순차열에 대한 프록시 반복자가 된다.

		$functor Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$functor Returns:
			새로 생성될 순차열의 원소.
	*/
	function __construct($functor, $iterable) {
		$this->functor = Functor($functor);

		if($this->mappable = $iterable instanceof Mappable)
			$iterable = $iterable->map($this->functor);

		$this->iterator = Iterator($iterable);
	}

	function valid() {
		return (bool) $this->iterator->valid();
	}

	function key() {
		return $this->valid() ? $this->iterator->key() : null;
	}

	function current() {
		if(!$this->valid())
			return null;

		return !$this->mappable ? $this->functor->call(
			$this->iterator->current(),
			$this->iterator->key()
		) : $this->iterator->current();
	}

	function next() {
		if($this->valid()) {
			$this->iterator->next();
			return $this->current();
		}
		else
			return null;
	}

	function rewind() {
		$this->iterator->rewind();
	}
}

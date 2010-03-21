<?php
/*
Title: Filter
	배열 등의 순차열에서 특정 조건의 원소만 걸러낼 때 사용한다. array_filter() 지연 평가(lazy evaluated) 반복자(Iterator) 버전.

Dependencies:
	- <Functor>
	- <Iterator>
	- <Filterable>

See Also:
	- <http://en.wikipedia.org/wiki/Filter_(higher-order_function)>
	- <http://docs.python.org/lib/built-in-funcs.html#l2h-28>
	- <http://www.ruby-doc.org/core/classes/Enumerable.html#M003156>
	- <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:filter>
*/
require_once dirname(__FILE__) . '/Functor.php';
require_once dirname(__FILE__) . '/Iterator.php';
require_once dirname(__FILE__) . '/Filterable.php';

/*
	Class: Filter
		Filter 클래스는 배열 등 반복 가능한 순차열(즉, 반복자를 구현한 객체)에서 특정 원소만 걸러낼 때 사용한다. Filter 자체도 Iterator의 일종이므로, Filter 인스턴스를 Filter하는 것도 가능하다.

		Filter가 array_filter()와 두 가지 다른 점이 있다. (<Transformation> 과 마찬가지.)

		- 표준 콜백 형식 외에도 Phunctional의 <Callable> 인스턴스도 허용한다는 것
		- 그 자신이 반복자(Iterator)의 일종이고, next() 메서드가 호출되기 전까지는 아무런 행동도 하지 않는다는 것—게으른 연산(lazy evaluation)

		두번째 특징은 일반적으로 좀 더 효율적인 성능을 내는 데에 도움을 준다. 하지만, 같은 연산(특정 원소를 거르는 것)을 연속적으로 해야한다면, iterator_to_array() 같은 함수를 이용해서 배열로 만들어놓고 그 배열을 계속 사용하는 것이 성능상 더 좋다. 같은 결과를 반환하는 연산을 무식하게 반복적으로 하기 때문이다.

		아래 코드는 배열 $numerics에서 정수형 값들만을 취하는 예제이다.

		(start code)
		$numerics = array(1, "2", 3, 3.14, "4", 5);
		$integers = new Filter('is_int', $numerics);
		(end)

		위 코드에서 유의할 점은 $integers는 배열이 아니라 반복자(Iterator)의 일종인 Filter 인스턴스라는 점이다. 즉, 저 시점에서는 우리가 의도하는 아무런 연산도 일어나지 않는다. 연산이 일어나는 것은 실제로 반복자가 작용할 때이다. 반복자는 foreach문이나 next() 메서드를 호출할 때, 혹은 Filter, <Transformation> 과 같이 내부적으로 next() 메서드를 호출하는 코드에서 작용한다.

		(start code)
		foreach($integers as $key => $integer)
			echo "$key: $integer\n";
		(end)

		위 코드의 foreach 부분에 이르러서야 $integers는 연산을 하게 된다. 위 코드는 아래와 같은 결과를 출력한다.

		(start code)
		0: 1
		2: 3
		5: 5
		(end)

		보다시피 Filter를 통과해도 배열 등 순차열의 키(key)는 그대로 보존된다.

		저 $integers를 곧바로 배열로 받고 싶다면, iterator_to_array() 함수를 사용하거나,

		(start code)
		$integers = iterator_to_array($integers);
		(end)

		처음부터 Filter 클래스를 사용하는 대신 array_filter() 함수를 사용해도 된다.

		(start code)
		$integers = array_filter('is_int', $numerics);
		(end)

		그러나 is_int 함수와 같은 간단한 표준 콜백으로는 표현하기 힘든 조건이 있을 수도 있다. 그럴 때는 역시 Filter를 쓰는 것이 훨씬 편하다. Filter는 표준 콜백 외에도 <Callable> 인터페이스를 구현한 함수자(predicate) 인스턴스들도 받기 때문이다.

		(start code)
		$words = array("hello", "5day-week", "expression", "0-defects", "sk8board", "nirvana");
		$non_digit_words = new Filter(bind1st('ereg', '[^[:digit:]]+'), $words);
		(end)

		$non_digit_words 반복자는 필요할 때 $words 배열에서 숫자가 포함되지 않은 문자열들만을 추출할 것이다.

		(start code)
		assert(
			array("hello", 2 => "expression", 5 => "nirvana")
			== iterator_to_array($non_digit_words)
		);
		(end)

	Implements:
		- Iterator
*/
class Filter implements Iterator {
	public $predicate;
	public $iterator;
	public $filterable;

	/*
		Constructor: __construct()

		Parameters:
			predicate $predicate - 인자로 원소 각각의 값과 키를 받고 조건에 따라 논리값(bool)으로 반환하는 서술자(predicate) 함수자.
			iterable|Filterable $iterable - 걸러낼 순차열. <Filterable> 인스턴스일 경우 생성된 <Filterable> 인스턴스는 $iterable->filter($predicate)가 반환하는 순차열에 대한 프록시 반복자가 된다.

		$predicate Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$predicate Returns:
			(bool) 원소를 포함시킬 경우 true, 제외시킬 경우 false.
	*/
	function __construct($predicate, $iterable) {
		$this->predicate = Functor($predicate);

		if($this->filterable = $iterable instanceof Filterable)
			$iterable = $iterable->filter($this->predicate);

		$this->iterator = Iterator($iterable);
		$this->rewind();
	}

	function valid() {
		return (bool) $this->iterator->valid();
	}

	function key() {
		return $this->valid() ? $this->iterator->key() : false;
	}

	function current() {
		return $this->valid() ? $this->iterator->current() : false;
	}

	function next() {
		if($this->filterable)
			$this->iterator->next();
		else if($this->valid()) {
			do {
				$this->iterator->next();

				if(!$this->iterator->valid())
					return null;
			}
			while(!$this->predicate->call($this->iterator->current(), $this->iterator->key()));
		}
		else
			return null;

		return $this->current();
	}

	function rewind() {
		$this->iterator->rewind();

		if(!$this->filterable and !$this->predicate->call($this->iterator->current(), $this->iterator->key()))
			$this->next();
	}
}

<?php
/*
Title: Zip

Dependencies:
	- <Iterator>

See Also:
	<http://docs.python.org/lib/built-in-funcs.html#l2h-81>
*/
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Class: Zip
		Zip 클래스는 여러 배열 등 여러 반복 가능한 순차열(즉, 반복자를 구현한 객체)의 원소 각각을 배열로 묶어 2차원 배열로 만들 때 쓴다. 즉, 2차원 배열의 수평적인 원소들을 수직적으
		로, 수직전인 원소들을 수평적으로 만든다.

		예를 들어 다음과 같은 2차원 배열이 있을 때,

		(start code)
		$source = array(array(1, 2, 3), array(4, 5, 6), array(7, 8, 9));
		(end)

		아래와 같이 Zip 인스턴스를 생성하면,

		(start code)
		$zip = new Zip($source);
		(end)

		iterator_to_array($zip)을 통해 $zip을 배열 형태로 늘어놓을 경우 아래와 같은 모양이 된다.

		(start code)
		array(
		array(1, 4, 7),
		array(2, 5, 8),
		array(3, 6, 9)
		)
		(end)

		만약 밖쪽의 배열에서 아래와 같이 키를 부여해서 $zip을 만들었다면,

		(start code)
		$zip = new Zip(array(
		'a' => array(1, 2, 3),
		'b' => array(4, 5, 6),
		'c' => array(7, 8, 9)
		));
		(end)

		$zip을 iterator_to_array($zip)을 통해 배열 형태로 늘어놓을 경우 아래와 같은 모양이 된다.

		(start code)
		array(
		array('a' => 1, 'b' => 4, 'c' => 7),
		array('a' => 2, 'b' => 5, 'c' => 8),
		array('a' => 3, 'b' => 6, 'c' => 9)
		)
		(end)

		안쪽의 순차열에 키가 있을 경우 그 키들은 버려진다. 원래 수평적이었던 원소들을 수직적으로 결합할 경우 서로 다른 순서와 다른 키를 가지고 있을 수 있기 때문이며, Zip은 그것을 >순서 기준으로 재정렬해버린다.


	Implements:
		- Iterator
*/
class Zip implements Iterator {
	public $iterators;
	protected $key;

	/*
		Constructor: __construct()

		Parameters:
			array $iterables - 원소를 묶을 평행한 순차열들.

		Dependencies:
			- <Zip>
	*/
	function __construct(array $iterables = array()) {
		$this->iterators = array_map('Iterator', $iterables);
		$this->rewind();
	}

	function valid() {
		if(!count($this->iterators))
			return false;

		foreach($this->iterators as $iter) {
			if(!$iter->valid())
				return false;
		}

		return true;
	}

	function key() {
		return $this->key;
	}

	function current() {
		$current = array();
		foreach($this->iterators as $key => $iter)
			$current[$key] = $iter->current();
		return $current;
	}

	function next() {
		if(!$this->valid())
			return false;

		foreach($this->iterators as $iter)
			$iter->next();

		++$this->key;
	}

	function rewind() {
		foreach($this->iterators as $iter)
			$iter->rewind();
		$this->key = 0;
	}
}

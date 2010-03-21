<?php
/*
Title: KeyIterator

Dependencies:
	- <Iterator>

See Also:
	- <http://php.net/manual/en/function.array-keys.php>
*/
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Class: KeyIterator
		순차열의 키들을 반환한다.

		(start code)
		$keys = new KeyIterator(array('a' => 97, 'b' => 98, 'c' => 99));

		assert(
			array('a', 'b', 'c')
			== iterator_to_array($keys)
		);
		(end)

	Implements:
		- Iterator
*/
class KeyIterator implements Iterator {
	public $iterator;
	protected $offset = 0;

	/*
		Constructor: __construct()

		Parameters:
			iterable $iterable - 키를 취할 순차열.
	*/
	function __construct($iterable) {
		$this->iterator = Iterator($iterable);
	}

	function current() {
		if(!$this->valid())
			return null;

		return $this->iterator->key();
	}

	function next() {
		++ $this->offset;
		$this->iterator->next();
	}

	function key() {
		return $this->valid() ? $this->offset : null;
	}

	function valid() {
		return !!$this->iterator->valid();
	}

	function rewind() {
		$this->offset = 0;
		$this->iterator->rewind();
	}
}

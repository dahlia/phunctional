<?php
/*
Title: Combination

Dependencies:
	- <Iterator>

See Also:
*/
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Function: Combination 
		키가 될 원소들의 순차열과 키와 대응하는 값이 될 원소들의 순차열을 받아 하나의 순차열로 만든다.

		(start code)
		$combined = new Combination('keys', xrange('a', 'd'));

		assert(
			array('k' => 'a', 'e' => 'b', 'y' => 'c', 's' => 'd')
			== iterator_to_array($combined)
		);
		(end)

	Parameters:
		iterable $keys - 키가 될 원소들의 순차열.
		iterable $values - 키에 대응하는 값이 될 원소들의 순차열.
*/
final class Combination implements Iterator {
	function __construct($keys, $values) {
		$this->keys = Iterator($keys);
		$this->values = Iterator($values);
	}

	function valid() {
		return $this->keys->valid() and $this->values->valid();
	}

	function next() {
		$this->keys->next();
		$this->values->next();
	}

	function current() {
		return $this->values->current();
	}

	function key() {
		return $this->keys->current();
	}

	function rewind() {
		$this->keys->rewind();
		$this->values->rewind();
	}
}

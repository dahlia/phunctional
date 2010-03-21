<?php
/*
Title: PaddingIterator

Dependencies:
	- <Iterator>

See Also:
	- <http://php.net/manual/en/function.array-pad.php>
*/
require_once dirname(__FILE__) . '/Iterator.php';

/*
	Class: PaddingIterator
		주어진 길이에 맞게 순차열을 밀어내고 밀린 자리를 입력한 값으로 채운다. 주어진 길이가 양수값일 경우 오른쪽에 여백을 추가하고, 음수값일 경우 길이는 절대값을 취하며 여백은 왼쪽에 추가합니다.

		(start code)
		$rightPadded = new PaddingIterator(xrange(0, 5), 10, -1);
		$leftPadded = new PaddingIterator(xrange(0, 5), -10, -1);

		assert(
			array(0, 1, 2, 3, 4, -1, -1, -1, -1, -1)
			== iterator_to_array($rightPadded)
		);
		assert(
			array(-1, -1, -1, -1, -1, 0, 1, 2, 3, 4)
			== iterator_to_array($leftPadded)
		);
		(end)

	Implements:
		- Iterator
*/
class PaddingIterator implements Iterator, Countable {
	public $iterator;
	protected $iterlen;

	public $length;
	public $value;

	protected $offset = 0;

	/*
		Constructor: __construct()

		Parameters:
			iterable $iterable - 밀어낼 순차열.
			number $length - 늘어날 길이.
			mixed $value - 밀린 자리에 들어갈 값.
	*/
	function __construct($iterable, $length, $value) {
		$this->iterator = Iterator($iterable);
		$this->iterlen = count($this->iterator);

		$this->length = $length;
		$this->value = $value;
	}

	function count() {
		return max(abs($this->length), $this->iterlen);
	}

	function current() {
		if(!$this->valid())
			return null;

		if($this->length > 0 && $this->iterator->valid()
		|| $this->length <= 0
		&& $this->offset >= count($this) - $this->iterlen
		)
			return $this->iterator->current();
		else
			return $this->value;
	}

	function next() {
		++ $this->offset;

		if($this->length > 0 && $this->iterator->valid()
		|| $this->length <= 0
		&& $this->offset > count($this) - $this->iterlen
		)
			$this->iterator->next();
	}

	function key() {
		return $this->valid() ? $this->offset : null;
	}

	function valid() {
		return $this->offset < count($this);
	}

	function rewind() {
		$this->offset = 0;
		$this->iterator->rewind();
	}
}

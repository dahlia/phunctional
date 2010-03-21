<?php
/*
Title: Range
*/
require_once dirname(__FILE__) . '/Reversible.php';

/*
	Class: Range
		반복자(Iterator)로 구현된 range()의 지연 평가(lazy evaulation) 버전.
		생성자의 시그너쳐 및 동작은 range()와 같다.

		(start code)
		$xrange = new Range(1, 15, 2);
		$range = range(1, 15, 2);
		assert(iterator_to_array($xrange) == $range);

		foreach($xrange as $i)
			echo "$i ";
		# output:
		#    1 3 5 7 9 11 13 15 

		foreach(new Range('d', 'a') as $letter)
			echo "$letter ";
		# output:
		#    d c b a
		(end)

	Implements:
		- Iterator

	See Also:
		- <http://kr2.php.net/manual/en/function.range.php>
		- <http://docs.python.org/lib/built-in-funcs.html#l2h-80>
*/
class Range implements Iterator, Reversible {
	public $begin;
	public $end;
	public $step;
	public $current;
	public $characterSequence;

	/*
		Constructor: __construct

		Parameters:
			number|string $begin - 순차열의 첫 원소.
			number|string $end - 순차열의 마지막 원소. (순차열는 이 값을 포함하는 양개구간이다.)
			(number $step) - 연속적인 원소들의 증가값. 양수여야 한다.
	*/
	function __construct($begin, $end, $step = 1) {
		if($this->characterSequence = is_string($begin) or is_string($end)) {
			$begin = is_string($begin) ? ord($begin) : $begin;
			$end = is_string($end) ? ord($end) : $end;
		}

		$this->begin = $begin;
		$this->end = $end;

		if($begin < $end)
			$this->step = abs($step);
		else
			$this->step = -abs($step);

		$this->rewind();
	}

	function rewind() {
		$this->current = $this->begin;
		$this->index = 0;
	}

	function current() {
		return $this->characterSequence ? chr($this->current) : $this->current;
	}

	function key() {
		return $this->index;
	}

	function next() {
		++$this->index;
		return $this->current += $this->step;
	}

	function valid() {
		if(!$this->step)
			return false;

		return $this->step > 0
			? $this->current <= $this->end
			: $this->current >= $this->end;
	}

	function reverse() {
		$step = abs($this->step);
		$begin = $this->begin;
		$end = $this->end;

		if($step === 1)
			$range = new Range($end, $begin);
		else {
			$rev = $begin > $end;
			$add = !$this->characterSequence ? $rev ? 1 : 0 : -1;

			$range = new Range(
				$end + ($rev ? 1 : -1) * $begin % $step + $add,
				$begin, $step
			);
		}

		$range->characterSequence = $this->characterSequence;

		return $range;
	}
}

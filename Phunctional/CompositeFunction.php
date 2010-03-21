<?php
/*
Title: CompositeFunction
	합성 함수.

Dependencies:
	- <Functor>

See Also:
	<http://en.wikipedia.org/wiki/Function_composition_%28computer_science%29>
*/
require_once dirname(__FILE__) . '/Functor.php';

/*
	Class: CompositeFunction
		합성 함수. 여러 함수를 연속적으로 적용하는 함수.

		(start code)
		$add4 = new Currying('+', array(4));
		$mul9 = new Currying('*', array(9));
		$add4_mul9 = new CompositeFunction($add4, $mul9);
		assert(63, $add4_mul9->call(3));
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class CompositeFunction extends Functor {
	public $functors;

	/*
		Constructor: __construct()

		Parameters:
			functor $functor... - 합성할 둘 이상의 함수자.
	*/
	function __construct($a, $b) {
		$args = func_get_args();

		$callables = array();
		foreach($args as $callable) if(!is_null($callable) or !$f = isset($f))
			$callables[] = $callable;

		$this->functors	= count($callables)
						? array_map('Functor', $callables)
						: IdentityFunction::getInstance();
	}

	function apply(array $args) {
		if(count($this->functors) < 1)
			return null;

		reset($this->functors);
		list(, $functor) = each($this->functors);
		$value = $functor->apply($args);

		while(list(, $functor) = each($this->functors))
			$value = $functor->call($value);

		return $value;
	}
}

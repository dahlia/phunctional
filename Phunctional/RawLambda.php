<?php
/*
Title: RawLambda

Dependencies:
	- <Functor>
*/
require_once dirname(__FILE__) . '/Functor.php';

/*
	Class: RawLambda
		람다 함수. <Lambda> 와 달리 클로져(closure)가 아니며 create_function()처럼 표현식을 문자열로 전달하여 생성한다. create_function()과 달리 인자명과 return 문을 쓰지 않아도 된다. $0, $1, $2, …의 형식으로 인수를 사용한다.

		(start code)
		$add = new RawLambda('$0 + $1');
		assert($add->call(1, 2) == 3);
		(end)

		<Shortcuts> 에서 정의된 <lambda()> 함수를 사용하면 더 짧게 쓸 수 있다.

		(start code)
		assert(lambda('$0 + $1')->call(1, 2) == 3);
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class RawLambda extends Functor {
	protected static $lambdas;
	protected $function;

	static function getParameters(array $parameters) {
		$result = array();
		foreach($parameters as $i => $value)
			$result["_$i"] = $value;
		return $result;
	}

	/*
		Constructor: __construct()

		Parameters:
			string $expression - PHP 표현식. 인수는 $0, $1, $2, … 형식으로 표현한다.
	*/
	function __construct($expression) {
		if(isset(self::$lambdas[$expression])) {
			$this->function = self::$lambdas[$expression];
			return;
		}

		$expression = preg_replace(
			'/\$(0|[1-9][0-9]*)/m',
			'$_\1',
			$_expr = $expression
		);

		$this->function = create_function('', '
			$_ = func_get_args();
			extract(RawLambda::getParameters($_));
			return ' . $expression . ';
		');
		self::$lambdas[$_expr] = $this->function;
	}

	function toCallback() {
		return $this->function;
	}

	function apply(array $args) {
		return call_user_func_array($this->function, $args);
	}
}

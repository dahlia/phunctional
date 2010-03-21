<?php
/*
Title: Functor
	Phunctional의 핵심.

	- <Callable>: Phunctional 프레임워크 모두에서 사용되는 유사 함수 객체에 대한 형식
	- <Functor()>, <Callback>: 기존 PHP의 레거시 형식과의 적응을 위한 요소들

	등을 포함한다.

License:
	LGPL 2.1
*/
version_compare(phpversion(), '5.1.0', '>=') or
	trigger_error('Phunctional requires PHP 5.1.0 or more', E_USER_ERROR);

extension_loaded('spl') or
	trigger_error('Phunctional requires SPL extension', E_USER_ERROR);

extension_loaded('reflection') or
	trigger_error('Phunction requires reflection extension', E_USER_ERROR);

extension_loaded('pcre') or
	trigger_error('Functor() requires PCRE extension', E_USER_ERROR);

/*
	Function: Functor()
		어떤 값이든 함수자(functor)—유사 함수 객체(function-like object), Phunctional에서는 <Callable> 인스턴스—로 만든다.

	Parameters:
		functor $functor - 함수자로 다형화될 수 있는 값이나 객체.

	Callable:
		$functor가 <Callable> 인스턴스일 경우, 그 인스턴스를 그대로 반환하게 된다. 이것은 <Functor()> 함수가 다형적인 캐스팅 용도로 사용되기 위한 기본적인 특성이다.

		(start code)
		assert($callable instanceof Callable);
		assert($callable === Functor($callablr));
		(end)

	Callback:
		$functor가 표준 콜백 형식(callback)일 경우 그에 해당하는 <Callback> 인스턴스를 반환한다.

		(start code)
		assert(new Callback('functionName') === Functor('functionName'));

		assert(
			new Callback($instance, 'method')
			=== Functor(array($instance, 'method'))
		);

		assert(
			new Callback('ClassName', 'method')
			=== Functor(array('ClassName', 'method'))
		);
		(end)

		$functor가 'ClassName::methodName' 형식의 문자열일 경우에도 <Callback> 인스턴스를 반환한다.

		(start code)
		assert(
			new Callback('ClassName', 'method') === Functor('ClassName::method')
		);
		(end code)

	HasFunctor:
		$functor가 <HasFunctor> 인스턴스일 경우, 이 <Functor()> 함수가 수용할 수 있는 형식의 값이나 인스턴스가 나올 때까지 <HasFunctor->toFunctor()> 메서드를 재귀적으로 호출하여, 반환된 값에 <Functor()> 함수를 적용한 <Callable> 인스턴스를 반환하게 된다.

		<HasFunctor>로부터 <Callable> 인스턴스를 구하는 과정을 간단하게 표현하면 아래와 같다.

		(start code)
		while($functor instanceof HasFunctor)
			$functor = $functor->toFunctor();
		$functor = Functor($functor);
		(end)

	CompositeFunction:
		$functor가 표준 콜백 형식이 *아닌* 배열이고 각 키들이 순차열일 경우, 각 배열의 원소를 functor 형식으로 보는 연속적인 합성 함수(<CompositeFunction>) 인스턴스를 반환합니다.

		(start code)
		$cf = Functor(array('trim', 'strrev', 'strtoupper'));
		assert('CBA' == $cf->call(' abc '));
		(end)

		배열의 각 요소는 functor 형식을 받으므로 <Callable> 인스턴스 등을 전달해도 된다.

		(start code)
		$cf = Functor(array('[key]', new Callback('trim'), 'strrev'));
		assert('cba' == $cf->call(array('key' => ' abc ')));
		(end)

		<Callback> 과의 모호성이 있을 경우 <Callback> 으로 우선적으로 인식되며, 모호성을 제거하기 위해 null을 사용할 수 있다. (null은 항등 함수(<IdentityFunction>)로 변환된다.)

		(start code)
		$cb = Functor(array('a', 'b'));
		assert($cb instanceof Callback);

		$cf = Functor(array('a', 'b', null));
		assert($cf instanceof Callable);
		(end)

	PartialApplication:
		$functor가 특정 패턴의 배열일 경우, 함수 객체에 인자를 고정한 새로운 함수 객체를 반환합니다. 이것을 <PartialApplication> 이라고 합니다.

		array('function_name' => array('a', 'r', 'g', 's'))와 같이 전달할 경우 function_name 함수의 앞쪽 네 인자를 'a', 'r', 'g', 's'로 고정한 <PartialApplication>을 반환합니다.

		(start code)
		$currying = Functor(array('is_string' => array('i am string')));
		assert($currying instanceof PartialApplication);
		assert($currying->call());

		$left = Functor(array('substr' => array(1 => 0)));
		assert($left instanceof PartialApplication);
		assert('hel' == $left->call('hello', 3));
		(end)

		그러나 배열의 키로는 문자열이나 숫자만 가능하기 때문에, 모든 함수 객체에 대해 고정이 가능한 것은 아닙니다. 따라서 원소가 둘인 배열을 전달하여 <PartialApplication> 이 가능한데, 각 원소의 역할은 키로 구분합니다.

		- 키가 'functor', 'function', 'func', 0 가운데 하나이면 그 원소의 값을 고정할 함수자로 여깁니다. 이것은 functor 형식으로, callable 형식이나 '->member[key]'와 같은 표현도 가능합니다.
		- 키가 'parameters', 'params', 'arguments', 'args', 'bindings', 'binding', 'bind', 'curryings', 'currying', 'curry', 1 가운데 하나이고, 그 원소의 값이 배열이면, 해당 배열을 고정할 인수들로 여깁니다.

		(start code)
		assert(
			Functor(array('is_string', array('i am string')))->call()
		);

		assert(
			!Functor(array(
				'functor' => 'is_string',
				'parameters' => array(123)
			))->call()
		);

		$currying = Functor(array('substr',
			'currying' => array(1 => 1, 2 => -1)
		));

		$expected = new PartialApplication('substr', array(1 => 1, 2 => -1));

		assert('ell' == $currying=>call('hello'));
		assert($expected === $currying);
		(end)

	Instantiation:
		'new ClassName' 형식의 문자열을 전달할 경우 ClassName 클래스를 생성하는 함수자인 new Instantiation('ClassName') 객체를 얻을 수 있다.

		(start code)
		$newInstance = Functor('new ClassName');

		assert($newInstance instanceof Instantiation);
		assert(new ClassName(1, 2, 3) == $newInstance->call(1, 2, 3));
		(end)

	Accessor:
		'->member', '->method()', '[key]', '->member[key]->method()' 등의 표현식으로 간단하게 객체나 값의 접근자 함수 객체를 생성해낼 수 있다.

		- $functor가 접근자 표현식 문자열일 때, "->"로 시작하고 "()"로 끝나는 문자열일 경우, <MethodInvoker> 인스턴스.
		- "->"로 시작하는 문자열일 경우, <MemberAccessor> 인스턴스를 반환한다.
		- "["로 시작하여 "]"로 끝나는 문자열일 경우, <ArrayAccessor> 인스턴스.
		- 접근자 표현식이 반복되는 경우, <CompositeFunction> 인스턴스.

		접근자 표현식은 반복될 수 있으며, PHP 해석기와 달리 메서드 호출 이후에 인덱스 접근('->method()[key]')을 해도 구문 오류가 나지 않는다.

		(start code)
		class Test {
			function a() {
				$object = new stdClass;
				$object->a = 123;
				return array('key' => $object);
			}
		}

		$array = array('object' => new Test);

		assert(123 == Functor('["object"]->a()[key]->a')->call($array));
		(end)

		인덱스 접근자 표현식([])에는 정수, 실수, 문자열 키 등을 사용할 수 있다.

		- [123]은 [(int) 123]으로 해석.
		- [3.14]는 [(float) 3.14]로 해석.
		- [key]는 ['key']로 해석.
		- ['key']는 ['key']로 해석.
		- ["key"]는 ["key"]로 해석.
		- ['\'key']는 ["'key"]로 해석.
		- ["\"key"]는 ['"key']로 해석.

	RawLambda:
		$functor가 "("로 시작하여 ")"로 끝나는 문자열일 경우, <RawLambda> 인스턴스를 반환한다. 즉 PHP 표현식을 괄호로 감싸고 있는 문자열을 <RawLambda> 로 인식한다.

		(start code)
		assert(8 == Functor('($0 + 1 * 2)')->call(3));
		(end)

	LanguageConstruct:
		언어 구조나 연산자를 문자열로 전달할 경우 아래와 같이 반환한다.

		- $functor가 언어 구조 문자열일 경우, <LanguageConstruct> 인스턴스.
		- $functor가 연산자 문자열일 경우, <Operator> 객체.

		(start code)
		assert(Functor('empty')->call(''));
		assert(6 == Functor('+')->call(1, 2, 3));
		assert(Functor('!')->call(false));
		assert(-123 == Functor('-')->call(123));
		assert(5 == Functor('-')->call(10, 5));
		(end)

	IdentityFunction:
		$functor가 null일 경우, 인수 그대로를 반환하는 항등 함수(<IdentityFunction> 인스턴스)를 반환한다. 이 특성은 고차 함수(higher-order function)를 작성할 때 함수자의 기본값을 null로 설정하는 식으로 사용되기 위한 것이기도 하다.

		(start code)
		assert(Functor(null)->call(true));
		assert(!Functor(null)->call(false));
		assert(123 == Functor(null)->call(123));
		(end)

	ConstantFunction:
		위 규칙들 가운데 어디에도 해당하지 않는 경우, $functor 값에 대한 상수 함수(<ConstantFunction>)를 반환한다. 상수 함수는 항상 같은 것만을 반환하기 때문에, 고차 함수를 사용하는 입장에서 유용하게 작동한다.

		(start code)
		assert(Functor(true)->call());
		assert(!Functor(false)->call());
		assert(123 == Functor(123)->call());
		(end)

	Returns:
		(<Callable>) 위 캐스팅 규칙들을 참고.

	Pseudo Types:
		functor - 이 <Functor()> 함수가 수용할 수 있는 형식의 값이나 인스턴스. 표준 콜백 형식 혹은 <Callable> 인스턴스 혹은 <HasFunctor> 인스턴스.

		(start code)
		access-expr := { x | is_string(x) } & /( -> (php-identifier) ( \( \) )? | [ .*? ])+/
		functor := access-expr | callback | Callable | HasFunctor
		callback := { x | is_callable(x) }
		(end)

	See Also:
		- <Callable>
		- <http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback>
		- <HasFunctor>
		- <Callback>
		- <IdentityFunction>
		- <ConstantFunction>
		- <MemberAccessor>
		- <MethodInvoker>
		- <CompositeFunction>
		- <LanguageConstruct>
		- <Operator>
		- <PartialApplication>
		- <RawLambda>
		- <Instantiation>
*/
function Functor($functor) {
	static $AccessorPattern = '{
		(
			\s* -> \s* ([a-z_][a-z0-9_]*)
				( \s* \( \s* \) )?
		|
			\s* \[ \s*
				(
					[^]]*
				|	" ( [^"\\\\] | \\\\. )* "
				|	\' ( [^\'\\\\] | \\\\. ) \'
				)
			\s* \]
		) \s*
	}imsx';

	while($functor instanceof HasFunctor and !($functor instanceof Callable))
		$functor = $functor->toFunctor();

	if($functor instanceof Callable)
		return $functor;

	if(is_string($functor)) {
		if(preg_match('/^\((.+)\)$/ms', $functor, $expr)) {
			require_once dirname(__FILE__) . '/RawLambda.php';
			return new RawLambda($expr[1]);
		}

		if(preg_match('/^\s*new\s+([_a-z][_a-z0-9]*)\s*$/ims', $functor, $name))
			return new Instantiation($name[1]);

		if(preg_match_all($AccessorPattern, $functor, $match, PREG_SET_ORDER)) {
			$functors = array();
			foreach($match as $expr) {
				switch(substr($expr[0], -1)) {
					case ']':
						if(is_numeric($expr[4]) or ereg('["\']$', $expr[4]))
							eval("\$key = $expr[4];");
						else
							$key = $expr[4];
						$functors[] = new ArrayAccessor($key);
						break;

					case ')':
						$functors[] = new MethodInvoker($expr[2]);
						break;

					default:
						$functors[] = new MemberAccessor($expr[2]);
						break;
				}
			}
		}
	}
	else if(is_array($functor) and (3 > $cnt = count($functor)) and $cnt > 0) {
		if($cnt == 2) {
			$rules = array(
				'functor' => array(0, 'functor', 'function', 'func'),

				'arguments' => array(
					1, 'parameters', 'params', 'arguments', 'args',
					'bindings', 'binding', 'bind',
					'curryings', 'currying', 'curry'
				)
			);

			$keys = array_keys($functor);

			for($i = 0; $i != 2; ++$i) {
				if(		in_array($keys[$i], $rules['functor'])
					and in_array($keys[1 - $i], $rules['arguments'])
					and is_array($functor[$keys[1 - $i]])
					and function_exists($functor[$keys[$i]])
				) {
					require_once dirname(__FILE__) . '/PartialApplication.php';

					return new PartialApplication(
						Functor($functor[$keys[$i]]),
						$functor[$keys[1-$i]]
					);
				}
			}
		}

		$key = key($functor);
		$value = current($functor);

		if(is_string($key) and is_array($value)) {
			require_once dirname(__FILE__) . '/PartialApplication.php';
			return new PartialApplication($key, $value);
		}
	}

	if(	!isset($functors)
		and is_array($functor)
		and (count($functor) > 2 or !is_callable($functor))
	)
		$functors = $functor;

	if(isset($functors) and is_array($functors)) {
		if(count($functors) == 1)
			return Functor(current($functors));

		require_once dirname(__FILE__) . '/CompositeFunction.php';
		$refclass = new ReflectionClass('CompositeFunction');
		return $refclass->newInstanceArgs($functors);
	}

	if(is_array($functor) and $functor[0] instanceof Callable and strtolower($functor[1]) == 'call')
		return $functor[0];

	if(is_string($functor)) {
		require_once dirname(__FILE__) . '/LanguageConstruct.php';

		if(in_array($functor, LanguageConstruct::$Keywords))
			return LanguageConstruct::getInstance($functor);

		try {
			return Operator::getInstance($functor);
		}
		catch(InvalidArgumentException $e) {
			# Do nothing.
		}
	}

	if(is_callable($functor, true))
		return new Callback($functor);
	else if(is_null($functor))
		return IdentityFunction::getInstance();

	return new ConstantFunction($functor);
}

/*
	Interface: Callable
		유사 함수 객체(function-like object) 인터페이스.
*/
interface Callable {
	/*
		Method: call()
			함수자(<Callable> 인스턴스)를 호출한다.
			functor(a, b, c)는 functor->call(a, b, c)로 표현된다.

		Parameters:
			$parameter... - 함수자 호출시 전달할 0개 이상의 인자 값들.

		Rerturns:
			함수자 호출 후 반환되는 값.
	*/
	function call();

	/*
		Method: apply()
			함수자(<Callable> 인스턴스)를 호출한다. 인자 값들을 배열로 전달한다.
			functor(a, b, c)는 functor->apply(array(a, b, c))로 표현된다.

		Parameters:
			array $parameters - 함수자 호출시 전달할 0개 이상의 인자 값들의 배열.

		Returns:
			함수자 호출 후 반환되는 값.
	*/
	function apply(array $parameters);

	/*
		Method: toCallback()
			표준 콜백 형식으로 값을 반환한다.
			이것은 usort() 등 기존 PHP에서 사용되던 표준 콜백 형식만을 지원하는 레거시 인터페이스에서도 Phunctional의 함수자 형식을 적응할 수 있게 해준다.

			(start code)
			assert($phunctor instanceof Callable);
			assert(is_array($array));
			usort($array, $phunctor->toCallback());
			(end)

		Returns:
			(callback) 표준 콜백 형식의 값.

		See Also:
			<http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback>
	*/
	function toCallback();
}

/*
	Interface: HasFunctor
		유연한 다형성을 위한 유사 함수자 인터페이스. 함수자를 반환하는 메서드를 포함한다. <Functor()> 함수에서는 이 인터페이스를 만족하는 인스턴스 역시 함수자로 취급한다.

		(start code)
		final class RecursiveHasFunctor implements HasFunctor {
			function __construct($functor) { $this->functor = $functor; }
			function toFunctor() { return $this->functor; }
		}

		$functor = Functor(
			new RecursiveHasFunctor(
				new RecursiveHasFunctor(
					new RecursiveHasFunctor(
						'is_string'
					)
				)
			)
		);

		assert($functor instanceof Callback);
		assert($functor->callback == 'is_string');
		(end)
*/
interface HasFunctor {
	/*
		Method: toFunctor()
			함수자를 반환한다.

		Returns:
			(callback|Callable|HasFunctor) 함수자 혹은 <HasFunctor> 인스턴스.
	*/
	function toFunctor();
}

/*
	Class: Functor
		함수자를 서브클래싱하기 위한 추상 클래스. *상속 받아서 사용한다.*

		함수자를 구현하기 쉽게 하기 위해 마련되어 있다. <Callable::call()> 및 <Callable::toCallback()> 이 이미 잘 작동하도록 구현되어 있으므로, 서브클래스에서는 <Callable::apply()> 만 구현하면 된다.

		(start code)
		class Adder extends Functor {
			public $number;

			function __construct($number) {
				$this->number = $number;
			}

			function apply(array $args) {
				return $this->number + $args[0];
			}
		}

		$add4 = new Adder(4);
		assert($add4->call(5) == 9);
		(end)

	Implements:
		- <Callable>
*/
abstract class Functor implements Callable {
	/*
		Method: call()
			자동으로 apply()로 전달되므로 <Callable::apply()> 메서드만 구현하면 된다. 오버라이드할 수 없다.
	*/
	final function call() {
		return $this->apply(func_get_args());
	}

	/*
		Method: toCallback()

		Returns:
			(callback) array($this, 'call'). 구현하지 않아도 알아서 표준 콜백 형식으로 변환된다.
			<Callback::toCallback()> 처럼 효율을 위해 오버라이드해서 구현할 수도 있다.
	*/
	function toCallback() {
		return array($this, 'call');
	}
}

/*
	Class: Callback
		표준 콜백 형식을 <Callable> 인터페이스로 랩핑한 클래스.

		생성자는 기본적으로 표준 콜백 형식을 지원한다.

		- 함수 이름으로 유효한 문자열의 경우, 해당 함수에 대한 콜백
		- 클래스 이름으로 유효한 문자열과 메서드 이름으로 유효한 문자열, 둘을 포함하는 배열일 경우, 해당 클래스 메서드에 대한 콜백
		- 인스턴스 객체와 메서드 이름으로 유효한 문자열, 둘의 포함하는 배열일 경우, 해당 인스턴스 메서드에 대한 콜백

		(start code)
		new Callback('abc'); # callback to abc()
		new Callback(array('abc', 'def')); # callback to abc::def()
		new Callback(array($abc, 'def')); # callback to $abc->def()
		(end)

		메서드에 대한 콜백의 경우, 첫번째 인자로 클래스 이름 문자열 혹은 인스턴스 객체를, 두번째 인자로 메서드 이름 문자열을 주어도 된다.

		(start code)
		new Callback('abc', 'def'); # callback to abc::def()
		new Callback($abc, 'def'); # callback to $abc->def()
		(end)

	Pseudo Types:
		callback - is_callable() 함수를 만족하는 값. 문자열 'function_name' 혹은 배열 array($instance, 'method_name') 혹은 array('ClassName', 'method_name').

		(start code)
		callback := { x | is_callble(x) }
		is_callable(x) := { x | is_string(x) and x is a function name,
			is_array(x) and x := array(a, b) and a is (a instance or a class name) and b is a method name }
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>

	See Also:
		<http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback>
*/
final class Callback extends Functor {
	const ClassMethodPattern = '^([a-zA-Z_][a-zA-Z0-9_]*)[[:space:]]*(::|->)[[:space:]]*([a-zA-Z_][a-zA-Z0-9_]*)$';

	protected static $argumentsLengthMap = array(
		'is_bool' => 1, 'is_string' => 1, 'is_int' => 1, 'is_float' => 1,
		'is_array' => 1, 'abs' => 1, 'sin' => 1, 'cos' => 1, 'tan' => 1
	);

	/*
		Property: $callback
			표준 콜백 형식 값. 내부적으로 이것을 호출한다.
	*/
	public $callback;

	/*
		Constructor: __construct()
			인수 하나만 전달할 경우 함수에 대한 콜백이 되며, 인수가 둘일 경우 인스턴스나 클래스의 메서드에 대한 콜백이 된다.

			(start code)
			new Callback('callback');
			new Callback('class_name', 'method');
			new Callback(array('class_name', 'method'));
			new Callback($instance, 'method');
			new Callback(array($instance, 'method'));
			new Callback('class_name::method');
			(end)
	*/	
	function __construct($a, $b = null) {
		if(!$b and ereg(self::ClassMethodPattern, (string) $a, $groups))
			$a = array($groups[1], $groups[3]);

		$this->callback = $b ? array($a, $b) : $a;
	}

	function apply(array $parameters) {
		$callback = $this->callback;

		$ref	= is_string($callback)
				? new ReflectionFunction($callback)
				: new ReflectionMethod($callback[0], $callback[1]);

		$args = $ref->getNumberOfParameters();

		if(is_string($callback) and isset(self::$argumentsLengthMap[$callback]))
			$args = self::$argumentsLengthMap[$callback];

		if(0 < $args)
			$parameters = array_slice($parameters, 0, $args);

		return call_user_func_array($callback, $parameters);
	}

	/*
		Method: toCallback()
			표준 콜백 형식의 값을 다시 반환한다. 인자로 받았던 해당 내용을 그대로 반환한다.

		Returns:
			(callback) $this->callback.

		See Also:
			<Callable::toCallback()>
	*/
	function toCallback() {
		return $this->callback;
	}
}

/*
	Class: Instantiation
		클래스의 실체화(instantiation)—인스턴스 생성에 대한 추상.

		(start code)
		class Test {
			public $a;
			public $b;

			function __construct($a, $b) {
				$this->a = $a;
				$this->b = $b;
			}
		}

		$Test = new Instantiation('Test');
		$aTest = $Test->call(1, 2);

		assert($aTest->a == 1);
		assert($aTest->b == 2);
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class Instantiation extends Functor {
	/*
		Property: $class
			클래스 이름.
	*/
	public $class;

	/*
		Constructor: __construct

		Parameters:
			string $class - 클래스 이름.
	*/
	function __construct($class) {
		if(!class_exists($class))
			throw new UnexpectedValueException("$class is not declared");
		$this->class = $class;
	}

	protected $refclass;

	function apply(array $args) {
		if(empty($this->refclass))
			$this->refclass = new ReflectionClass($this->class);
		return $this->refclass->newInstanceArgs($args);
	}
}

/*
	Class: IdentityFunction
		항등 함수. 하나의 인수를 받아 해당 값을 그대로 반환하는 함수 객체. 싱글톤 클래스로, 인스턴스는 new 키워드를 통해서 생성하는 대신 <IdentityFunction::getInstance()> 메서드를 사용한다.

		(start code)
		$value = rand();
		assert($value == IdentityFunction::getInstance()->call($value);
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
final class IdentityFunction extends Functor {
	protected static $instance;

	/*
		Constructor: getInstance()
			<IdentityFunction> 인스턴스를 반환한다. new 키워드 대신 이 
메서드를 사용한다. 항상 동일한 인스턴스를 반환한다.
	*/
	static function getInstance() {
		return empty(self::$instance)
			? self::$instance = new self
			: self::$instance;
	}

	protected function __construct() {
		# Cannot instantiate without getInstance() method.
	}

	function apply(array $parameters) {
		if(count($parameters) < 1)
			throw new InvalidArgumentException('Missing one parameter');

		return $parameters[0];
	}
}

/*
	Class: ConstantFunction
		상수 함수. 항상 하나의 값만을 반환한다. 호출시 인수들은 무시된다.

		(start code)
		$randomNumber = rand();
		$getNumber = new ConstantFunction($randomNumber);

		assert($getNumber->call() == $randomNumber);
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
final class ConstantFunction extends Functor {
	public $value;	

	/*
		Constructor: __construct()

		Parameters:
			$value - 해당 상수 함수가 항상 반환할 값
	*/
	function __construct($value) {
		$this->value = $value;
	}

	function apply(array $_) {
		return $this->value;	
	}
}

/*
	Class: MemberAccessor
		인수로 받는 객체의 특정 멤버 변수(member variable)를 반환하는 함수자.

		(start code)
		class Test {
			public $value;
			function __construct($value) {
				$this->value = $value;
			}
		}

		$getValue = new MemberAccessor('value');
		$test = new Test('abc');
		assert($getValue->call($test) == 'abc');
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class MemberAccessor extends Functor {
	public $member;

	/*
		Constructor: __construct()

		Parameters:
			string $member - 함수자 호출시 인자로 받은 객체로부터 가져올 멤버 이름.
	*/
	function __construct($member) {
		$this->member = $member;
	}

	function apply(array $args) {
		return $args[0]->{$this->member};
	}
}

/*
	Class: MethodAccessor
		인수로 받는 객체의 특정 인스턴스 메서드에 대한 콜백을 반환하는 함수자.

		(start code)
		class Test {
			protected $value;

			function __construct($value) {
				$this->value = $value;
			}

			function getValue() {
				return $this->value;
			}
		}

		$getGetValue = new MethodAccessor('getValue');
		$test = new Test('abc');
		assert($getGetValue->call($test)->call() == 'abc');
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class MethodAccessor extends Functor {
	public $method;

	/*
		Constructor: __construct()

		Parameters:
			string $method - 인자로 받은 객체의 인스턴스 메서드에 대한 콜백을 가져올 이름.
	*/
	function __construct($method) {
		$this->method = $method;
	}

	function apply(array $args) {
		if(method_exists($args[0], $this->method))
			return new Callback($args[0], $this->method);
		return null;
	}
}

/*
	Class: MethodInvoker
		인수로 받는 객체의 특정 인스턴스 메서드를 호출하는 함수자.

		(start code)
		class Test {
			protected $value;

			function __construct($value) {
				$this->value = $value;
			}

			function getValue() {
				return $this->value;
			}
		}

		$invokeGetValue = new MethodInvoker('getValue');
		$test = new Test('abc');
		assert($invokeGetValue->call($test) == 'abc');
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class MethodInvoker extends Functor {
	public $method;
	public $parameters;

	/*
		Constructor: __construct()

		Parameters:
			string $method - 인자로 받은 객체의 호출할 메서드 이름.
			array $parameters - 호출할 메서드에 전달한 인수들.
	*/
	function __construct($method, array $parameters = array()) {
		$this->method = $method;
		$this->parameters = $parameters;
	}

	function apply(array $args) {
		return call_user_func_array(
			array($args[0], $this->method),
			$this->parameters
		);
	}
}

/*
	Class: ArrayAccessor
		인수로 받는 배열이나 ArrayAccess 인스턴스의 특정 키(key)에 대한 값을 반환하는 함수자.

		(start code)
		$getValue = new ArrayAccessor('key');
		assert($getValue->call(array('key' => 'abc')) == 'abc');
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
class ArrayAccessor extends Functor {
	public $key;

	/*
		Constructor: __construct()

		Parameters:
			string|number $key - 함수자 호출시 인자로 받은 배열이나 ArrayAcces 인스턴스로부터 가져올 키.
	*/
	function __construct($key) {
		$this->key = $key;
	}

	function apply(array $args) {
		return $args[0][$this->key];
	}
}

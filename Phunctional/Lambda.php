<?php
/*
Title: Lambda

Dependencies:
	- <Functor>
*/
require_once dirname(__FILE__) . '/Functor.php';
ini_set('allow_call_time_pass_reference', 'On');

/*
	Class: Lambda
		람다 함수. create_function()이나 <RawLambda> 와는 달리, 표현식 코드를 문자열로 전달하는 대신, 실제 코드로 작성하여 사용한다. 또한 클로져이다. 구체적으로는 삼항 연산자를 통해 사용하는데, 예제 코드를 보자.

	(start code)
	$add = Lambda::begin($a, $b)?
		$a + $b
	:Lambda::end();

	assert($add->call(1, 2) == 3);
	(end)

	삼항 연산자의 첫 피연산자 표현식에는 람다 함수라는 명시와 함께 람다 함수의 시그니쳐를 표시하게 된다. 이때 기존 함수와 다른 두 가지가 있다.

	- 타입 힌트(type hint)를 사용할 수 없다.
	- 인자 기본값은 상수 표현식이 아니여도 된다. 즉 $a = $x + 2와 같이 기본값을 부여할 수 있다. 그러나 해당 표현식은 람다 함수가 호출될 때 평가되는 것이 아니라, 람다 함수가 생성될 때 평가된다.

	두번째 피연산자로 실제 람다 함수의 표현식 코드가 삽입되게 되는데, 실제로 첫번째 피연산자로 들어가는 Lambda::begin() 함수는 항상 false를 반환하기 때문에, 생성 당시에는 평가되지 않고, 세번째 피연산자 표현식을 평가하게 된다.

	세번째 피연산자의 Lambda::end()는 내부적으로 Lambda::begin()에서 생성된 람다 함수자 객체를 보관하고 있다가 반환한다.

	결과적으로 삼항연산자 표현식 전체는 람다 함수자 객체로 평가될 것이다. 실제로 람다 함수자를 반환하는 것은 마지막 세번째 피연산자 표현식이므로, 삼항연산자 표현식 전체를 괄호로 감싸지 않아도 메서드 체이닝이 가능하다.

	위 코드를 줄이면 아래와 같이 할 수 있다. 익명 함수이고, 값으로서의 함수이므로 굳이 이름을 부여할 필요가 없이 바로 사용할 수 있다.

	(start code)
	assert(Lambda::begin($a, $b)? $a + $b :Lambda::end()->call(1, 2) == 3);
	(end)

	클로져이기 때문에, 람다가 생성되는 문맥에서의 변수들을 호출시에도 똑같이 사용할 수 있다. 아래 코드를 보자.

	(start code)
	$x = 123;
	$add_x = Lambda::begin($a)?
		$a + $x
	:Lambda::end();

	assert($add_x->call(1) == 124);

	++$x;
	assert($add_x->call(2) == 125);
	(end)

	함수 밖에서 생성된 람다일 경우, 전역 변수 모두에 접근이 가능하지만, 함수 안쪽에서 생성된 람다일 경우 클로즈되는 변수는 해당 함수가 받는 인자들에 한정된다.

	(start code)
	function getAdder($x) {
		return Lambda::begin($a)?
			$a + $x
		:Lambda::end();
	}

	assert(getAdder(123)->call(1) == 124);
	(end)

	위 코드에서 $x는 람다가 생성된 문맥인 getAdder() 함수의 인자이기 때문에 성공적으로 클로즈될 수 있었다.

	(start code)
	function getAdder() {
		$x = 123;
		return Lambda::begin($a)?
			$a + $x
		:Lambda::end();
	}

	assert(getAdder()->call(1) != 124);
	(end)

	그러나 위 코드에서의 $x 변수는 getAdder() 함수 안에서 새롭게 생성된 변수이기 때문에 클로즈되지 못한다. 결국 람다 함수 안쪽에서 $x 변수는 전달되지 않게 되어, 정의되지 않은 변수로 취급된다.

	(start code)
	function getAdder() {
		static $x = 123;
		return Lambda::begin($a)?
			$a + $x
		:Lambda::end();
	}

	assert(getAdder()->call(1) == 124);
	(end)

	다만 함수의 정적 변수의 경우는 성공적으로 클로즈된다.

	이와 같이 함수 안쪽에서 새롭게 생성된 변수는 클로즈하지 못하는 문제점은 어떤 의도에 의한 것이 아니라, PHP에서의 어쩔 수 없는 한계 때문에 생긴 것으로, 추후 완전한 클로져로 거듭날 가능성도 없지 않다.

	대신 명시적으로 클로즈할 변수와 값들을 지정하는 방법은 있다.

	(start code)
	function getAdder() {
		$x = 123;

		$closure = Lambda::begin($a)?
			$a + $x
		:Lambda::end();

		$closure->context += compact('x');
		return $closure;
	}

	assert(getAdder()->call(1) != 124);
	(end)

	위 코드에서 compact('x')는 array('x' => $x) 표현식으로 치환해도 동일한 동작을 한다. 그러나 앞으로의 변화 가능성이나 중복 등을 고려한다면 compact() 함수를 사용하는 편이 좀더 나을 것이다.

	<Shortcuts> 에서 정의된 <def()> 함수와 <fed()> 함수를 사용하면 좀더 간단히 람다 함수를 생성할 수 있다.

	(start code)
	$add = def($a, $b)?
		$a + $b
	:fed();

	assert($add->call(1, 2) == 3);
	(end)

	람다 안쪽에서 람다를 생성하여 반환하는 고차 함수도 가능하다.

	(start code)
	$g = 1;

	$outer = def($a)?
		def($b)? $g + $a + $b :fed()
	:fed();

	var_dump($outer->call(10)->call(100));
	(end)

	위 코드는 int(111)을 출력한다.

	오류가 나는 줄도 제대로 짚어준다.

	(start code)
	var_dump($outer->call(10)->call());
	(end)

	위와 같이 시그너쳐에 맞지 않는 호출을 했을 경우,

	(start code)
	Warning: Missing argument 1 for Lambda functor, called in file.php on line 7 and instantiated in file.php on line 4 in Phunctional/Lambda.php on line 334
	(end)

	클로져 안쪽에서 생성된 함수라도 해당 라인을 정확히 짚어준다. (마지막 in Phunctional/Lambda.php on line 334 메세지는 trigger_error() 함수를 이용했을 때 어쩔 수 없이 껴버리는 부분으로, 무시하도록 한다.)

	Implements:
		- <Callable>
*/
final class Lambda implements Callable {
	protected static $begun = null;
	protected static $files = array();
	protected $parameters;
	public $context = array();
	public $arguments = array();
	public $defaultParameters = array();
	public $instantiatedFile;
	public $instantiatedLine;

	/*
		Function: begin()
			삼항 연산자의 첫 피연산자로 사용되며, 람다 함수의 생성을 시작하겠다는 의미를 지닌다. 인수로 생성될 함수자의 시그너쳐를 정의한다.

		Parameters:
			이 클래스 메서드의 인수들은 일반적인 함수들처럼 평가되지 않으며, 생성될 함수자의 시그너쳐를 정의하기 위해 사용한다. 인수들은 생성될 람다 함수의 시그니쳐가 되며, 타입 힌트(type hint)를 지원하지 않고, 기본값을 지원한다.

			- $로 시작되는 변수명이 아닐 경우 무시된다.
			- 변수명은 해당 순서의 인자로 해석된다.
			- 변수명 뒤에 대입 연산자(=)와 표현식이 있을 경우, 해당 인자의 기본값으로 해석된다.

			(start code)
			Lambda::begin($a, $b, $c)
			(end code)

			위 코드는 생성될 람다 함수가 세 개의 인자를 받는다는 것을 명시한다. 함수 안쪽에서는 $a, $b, $c라는 변수명으로 접근이 가능할 것이다.

			(start code)
			Lambda::begin(&$ref)
			(end code)

			참조로 명시를 하는 것도 가능은 하지만, 실제로 참조가 동작하지는 않는다.

			(start code)
			Lambda::begin($a, $b, $c = "default " . "value")
			(end code)

			위 코드에서 생성된 람다는 두 개의 필수 인자와, 하나의 선택 인자를 갖게 된다. 마지막 인자의 기본 값은 "default value"가 된다. 일반 함수의 정의에서는 인자 기본값으로 상수 표현식만 들어갈 수 있으나, 람다 생성시에는 아무 표현식이나 가능하다.

			(start code)
			Lambda::begin($a, $b = "default value", $c)
			(end)

			위 코드에서 $a는 필수 인자가 되지만, $b와 $c는 선택 인자가 된다. 선택 인자 이후에는 필수 인자가 올 수 없으며, 선택 인자가 하나라도 나온 이후의 인자는 기본값이 명시되어 있지 않을 경우 null이 기본값이 된다.

			(start code)
			Lambda::begin($var, Constant, 3.14, 'string', functionCall(), &$reference);
			(end)

			변수 이름이 아닌 경우에는 모두 무시된다. 위 코드의 람다 함수는 $var, $reference, 두 개의 인자만을 가지게 된다. 나머지 표현식은 무시된다. $reference의 경우에도 실제로 참조로 전달되는 인자가 되는 것은 아니다.

		Returns:
			(bool) 항상 false.
	*/
	static function begin() {
		$params = func_get_args();
		return self::generate(debug_backtrace(), $params);
	}

	/*
		Function: generate()
			실제로 Lambda 인스턴스를 생성하는 클래스 메서드. <Lambda::begin()> 메서드는 내부적으로 이 메서드를 호출한다. *실제로 이 메서드를 호출할 일은 없다. 무엇인지 알지 못하겠다면 사용하지 않는 것이 좋다.*

			(start code)
			function def() {
				$params = func_get_args();
				return self::generate(debug_backtrace(), $params);
			}
			(end)

		Parameters:
			array $backtrace - 코드 분석(code inspection)을 위한 스택 트레이스 정보. debug_backtrace() 함수나 Exception->getTrace() 메서드를 통해 받을 수 있다.
			array $defaultParameters - 인자 기본값으로 사용될 값들의 배열. func_get_args() 함수가 반환하는 것을 그대로 전달하면 된다.

		Returns:
			(bool) 항상 false.
	*/
	static function generate(array $stack, array $defaultParameters = array()) {
		if(self::$begun)
			trigger_error('Lambda has not closed', E_USER_WARNING);

		$stack = self::removeIncludeStacksFromBacktrace($stack);
		$F = 'function';
		$C = 'class';

		if(count($stack) > 1) {
			$call = $stack[1];

			if($call[$F] == 'eval' and self::isCalledInLambda($stack[2])) {
				$lambda = $stack[2]['object'];

				$context = array_merge(
					$lambda->context,
					$lambda->getParameters()
				);
			}
			else {
				$parameters	= $call['args'];
				$reflection	= isset($call[$C])
							? new ReflectionMethod($call[$C], $call[$F])
							: new ReflectionFunction($call[$F]);

				$vars = array();
				foreach($reflection->getParameters() as $_param)
					$vars[] = $_param->getName();

				$context = array();
				for($i = 0; isset($parameters[$i]) and isset($vars[$i]); ++$i)
					$context[$vars[$i]] = $parameters[$i];

				$context = array_merge(
					$context,
					$reflection->getStaticVariables()
				);
			}
		}
		else
			$context = $GLOBALS;

		self::$begun = new self(
			$stack[0],
			$defaultParameters,
			$context,
			isset($lambda) ? $lambda : null
		);

		return false;
	}

	/*
		Function: end()
			삼항 연산자의 마지막 피연산자로 들어가며, 실제 람다 함수 객체를 반환한다.

		Returns:
			<Lambda::begin()> 메서드에서 생성한 람다 함수.
	*/
	static function end(array $context = array()) {
		$lambda = self::$begun;
		self::$begun = null;
		$lambda->context = array_merge($lambda->context, $context);
		return $lambda;
	}

	protected static function isNotIncludeStack(array $call) {
		return !in_array(
			strtolower($call['function']),
			array('include', 'include_once', 'require', 'require_once')
		);
	}

	protected static function removeIncludeStacksFromBacktrace(array $backtrace) {
		return array_values(array_filter(
			$backtrace,
			array(__CLASS__, 'isNotIncludeStack')
		));
	}

	protected static function isCalledInLambda(array $call) {
		return (
			$call['class'] == __CLASS__ and
			$call['function'] == 'call' and
			$call['type'] == '->'
		);
	}

	protected static function getFile($filename) {
		if(isset(self::$files[$filename]))
			return self::$files[$filename];

		return self::$files[$filename] = file_get_contents($filename);
	}

	protected function __construct(
		array $sender,
		array $defaultParameters = array(),
		array $context = array(),
		self $instantiatedInLambda = null
	) {
		if($instantiatedInLambda) {
			$file = $instantiatedInLambda->instantiatedFile;
			$line = $instantiatedInLambda->instantiatedLine;
			$code = $instantiatedInLambda->code;
		}
		else {
			$file = $sender['file'];
			$line = $sender['line'];

			$_ = $line - 1;

			$code = preg_replace(
				"/^([^\n]*\n){{$_}}/", '',
				self::getFile($file)
			);
		}

		if(isset($sender['class']) and $sender['type'] == '::')
			$pattern = $sender['class'] . '\s*::\s*' . $sender['function'];
		else if(isset($sender['class']) and $sender['type'] == '->')
			$pattern = '->\s*' . $sender['function'];
		else
			$pattern = $sender['function'];

		preg_match("/$pattern\s*\\(/i", $code, $regs);
		$offset = strpos($code, $regs[0]) + strlen($regs[0]);
		$line += substr_count($code, "\n", 0, $offset);
		$code = substr($code, $offset);

		$pattern = '/^\s*&?\\$([a-zA-Z_][a-zA-Z0-9_]*)\s*(=.+)?$/';

		for(
			$i = 0, $len = strlen($code), reset($defaultParameters);
			$i < $len and ($i <= 0 or $code[$i - 1] != ')');
			$i += $expr->length + 1, next($defaultParameters)
		) {
			$expr = new ExpressionPicker(substr($code, $i), array(',', ')'));

			if(preg_match($pattern, $expr->code, $regs)) {
				$this->arguments[] = $regs[1];

				if(!empty($regs[2]) or isset($beginDefaultParameter)) {
					$this->defaultParameters[] = current($defaultParameters);
					$beginDefaultParameter = true;
				}
			}
		}

		$expr = new ExpressionPicker(
			substr($code, strpos($code, '?', $i) + 1),
			':'
		);

		$this->code = $expr->code;
		$this->context = $context;
		$this->instantiatedFile = $file;
		$this->instantiatedLine = $line;
	}

	protected function getParameters() {
		$argc = count($this->arguments);
		$paramc = count($this->parameters);

		if($argc > $paramc) {
			$this->parameters = array_merge(
				$this->parameters,
				array_slice($this->defaultParameters, $paramc - $argc)
			);
		}

		for(
			$i = 0, $pairs = array();
			isset($this->parameters[$i]) and isset($this->arguments[$i]);
			$pairs[$this->arguments[$i]] = $this->parameters[$i++]
		);

		return $pairs;
	}

	protected function validateParameters(array $stack, array $params = null) {
		if(!is_array($params))
			$params = $this->parameters;

		$argc = count($params);

		if($argc >= count($this->arguments) - count($this->defaultParameters))
			return true;

		++$argc;
		$file = $stack[0]['file'];
		$line = $stack[0]['line'];

		$message	= "Missing argument $argc for Lambda functor, "
					. "called in $file on line $line "
					. "and instantiated in {$this->instantiatedFile} "
					. "on line {$this->instantiatedLine}";

		trigger_error($message, E_USER_WARNING);
		return false;
	}

	function close(array $context = array()) {
		$lambda = clone $this;
		$lambda->context = array_merge($lambda->context, $context);
		return $lambda;
	}

	function call() {
		$this->parameters = func_get_args();

		if(!$this->validateParameters(debug_backtrace()))
			return;

		try {
			extract($this->context);
			extract($this->getParameters());		

			eval("\$value = ({$this->code});");
		}
		catch(Exception $e) {
			unset($this->parameters);
			throw $e;
		}

		unset($this->parameters);
		return $value;
	}

	function apply(array $params) {
		if($this->validateParameters(debug_backtrace(), $params))
			return call_user_func_array($this->toCallback(), $params);
	}

	function toCallback() {
		return array($this, 'call');
	}
}

final class ExpressionPicker {
	static $pairs = array(
		'(' => ')',
		'[' => ']',
		'{' => '}',
		'?' => ':',
		'"' => '"',
		"'" => "'",
		'/*' => '*/',
		'//' => "\n",
		'#' => "\n"
	);

	static $escapes = array(
		'"' => '\\',
		"'" => '\\'
	);

	static $endsExceptions = array(
		'::', '->', '=>',
		'==', '!=', '<>', '===', '!==', '<=', '>=',
		'<<', '>>',
		'&&', '||'
	);

	public $code;
	public $length;
	protected $stack = array();
	protected $top = null;
	protected $events = array();
	protected $escaping = false;

	function __construct($code, $endsWith = null) {
		$endsWith = (array) $endsWith;
		$this->refresh();

		for($i = 0, $len = strlen($code); $i < $len; ++$i) {
			if($this->escaping) {
				$this->escaping = false;
				continue;
			}

			foreach($this->events as $handle => $method) {
				if(substr($code, $i, strlen($handle)) != $handle)
					continue;

				$this->$method($handle);
				continue 2;
			}

			if(count($this->stack))
				continue;

			foreach(self::$endsExceptions as $seq) {
				if(substr($code, $i, strlen($seq)) == $seq) {
					++$i;
					continue 2;
				}
			}

			foreach($endsWith as $seq) {
				if(substr($code, $i, strlen($seq)) == $seq)
					break 2;
			}
		}

		$this->length = $i;
		$this->code = substr($code, 0, $i);
	}

	protected function push($char) {
		$top = count($this->stack) - 1;
		if(!isset($this->stack[$top]) or !isset(self::$escapes[$this->stack[$top]]))
			$this->stack[] = $char;
		$this->refresh();
	}

	protected function pop() {
		$pop = array_pop($this->stack);
		$this->refresh();
		return $pop;
	}

	protected function refresh() {
		$this->top = count($this->stack) ? end($this->stack) : null;
		$this->events = array();

		foreach(array_keys(self::$pairs) as $key)
			$this->events[$key] = 'push';

		if($this->top) {
			$this->events[self::$pairs[$this->top]] = 'pop';

			if(isset(self::$escapes[$this->top]))
				$this->events[self::$escapes[$this->top]] = 'ignoreNext';
		}
	}

	protected function ignoreNext() {
		$this->escaping = true;
	}
}

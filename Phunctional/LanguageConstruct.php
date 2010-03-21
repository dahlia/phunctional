<?php
/*
Title: LanguageConstruct 
	연산자와 언어 구조를 랩핑한 함수자 클래스들을 제공한다.

Dependencies:
	- <Functor>
*/
require_once dirname(__FILE__) . '/Functor.php';

/*
	Class: Operator
		연산자를 함수자로 사용할 수 있게 해준다.

		지원하는 연산자는 아래와 같다.

		- + (여러 인자를 전달할 경우 모든 값을 더한 값을 반환한다.)
		- - (인자를 하나만 전달할 경우에는 양음수 전환을 하는 전위 연산자로 작동하고, 두 인자를 전달할 경우에는 빼기 이항 연산자로 작동한다.)
		- *, /, %, .
		- &, |, ^, <<, >>
		- ==, ===, !=, <>, !==, <, >, <=, >=
		- and, &&, or, ||, xor, !
		- ++, -- (증감 연산자는 전위 연산자로만 작동한다. 즉, 먼저 값을 증감시킨 후, 그 값을 반환한다.)

		함수자는 new 키워드를 통해 생성하는 대신 <Operator::getInstance()> 메서드에 사용할 연산자를 문자열로 전달하여 객체를 받는다.

	Extends:
		<Functor>

	Implements:
		- <Callable>

*/
abstract class Operator extends Functor {
	private static $instances;
	private static $operatorTypes = null;
	public $operator;

	/*
		Function: getInstance()
			연산자 함수 객체를 구한다. 반환되는 객체는 각 종류의 연산자에 하나씩만 생성된다.

		Parameters:
			string $operator - 구할 연산자

		Returns:
			- 뺄셈(-) 연산자의 경우 <MinusOperator> 인스턴스
			- 단항연산자일 경우 <UnaryOperator> 인스턴스
			- 이항연산자일 경우 <BinaryOperator> 인스턴스
	*/
	final static function getInstance($operator) {
		if(!is_string($operator))
			throw new InvalidArgumentException('Expected operator symbol string');

		if(!isset(self::$instances[$operator])) {
			if(is_null(self::$operatorTypes)) foreach(get_declared_classes() as $c) if(is_subclass_of($c, __CLASS__))
				self::$operatorTypes[] = $c;

			foreach(self::$operatorTypes as $Class) {
				$refprop = new ReflectionProperty($Class, 'Operators');
				if(in_array($operator, $refprop->getValue()))
					$OperatorType = $Class;
			}

			if(!isset($OperatorType))
				throw new InvalidArgumentException("$operator operator symbol is unsupported or invalid");

			self::$instances[$operator] = new $OperatorType($operator);
		}

		return self::$instances[$operator];
	}

	protected function __construct($operator) {
		$this->operator = $operator;
	}
}

/*
	Class: UnaryOperator
		단항연산자를 제공한다. 함수 객체를 직접 생성하지 않고 <Operator::getInstance()> 메서드를 사용해서 받는다.

		아래의 연산자들을 지원한다.

		- 논리 연산자: !
		- 비트 연산자: ~
		- 증감 연산자: ++, --

		(start code)
		$accum = Operator::getInstance('++');
		assert($accum->call(5) == 6);
		(end)

	Extends:
		<Operator>

	Implements:
		- <Callable>
*/
final class UnaryOperator extends Operator {
	static $Operators = array('!', '~', '++', '--');

	function apply(array $operands) {
		eval('$result = ' . $this->operator . ' $operands[0];');
		return $result;
	}
}

/*
	Class: BinaryOperator
		단항연산자를 제공한다. 함수 객체를 직접 생성하지 않고 <Operator::getInstance()> 메서드를 사용해서 받는다.

		아래의 연산자들을 지원한다.

		- 뺄셈을 제외한 숫자 계산 연사자들: +, *, /. %
		- 문자열 연결 연삱: .
		- 비트 연산자: &, |, ^
		- 쉬프트 연산자: <<, >>
		- 비교 연산자: ==, ===, !=, <>, !==, <, >, <=, >=
		- 논리 연산자: and, &&, or, ||, xor

		(start code)
		$sum = Operator::getInstance('+');
		assert($sum->call(1, 2) == 3);
		assert($sum->call(1, 2, 3) == 6);
		(end)

	Extends:
		<Operator>

	Implements:
		- <Callable>
*/
class BinaryOperator extends Operator {
	static $Operators = array(
		'+', '*', '/', '%', '.',
		'&', '|', '^', '<<', '>>',
		'==', '===', '!=', '<>', '!==', '<', '>', '<=', '>=',
		'and', '&&', 'or', '||', 'xor'
	);

	protected static $DefaultOperands = array(
		'%' => 0,
		'&' => 0,
		'>>' => 0,
		'<<' => 0
	);

	function apply(array $operands) {
		if(count($operands) < 2 and isset(self::$DefaultOperands[$this->operator]))
			return self::$DefaultOperands[$this->operator];

		$result = array_shift($operands);
		foreach($operands as $operand)
			eval('$result = (($result) ' . $this->operator . ' ($operand));');
		return $result;
	}
}

/*
	Class: MinusOperator
		뺄셈연산자를 제공한다. 함수 객체를 직접 생성하지 않고 <Operator::getInstance()> 메서드를 사용해서 받는다.

		피연산자(인수)의 갯수에 따라 작동이 다르다.

		- 하나만 전달될 경우, 단항연산자로 작동하여 음수일 경우 양수를, 양수일 경우 음수를 반환한다.
		- 둘 이상 전달될 경우, 뺄셈연산자로 작동한다.

		(start code)
		$minus = Operator::getInstance('-');
		assert($minus->call(123) == -123);
		assert($minus->call(1, 2) == -1);
		(end)

	Extends:
		<Operator>

	Implements:
		- <Callable>
*/
final class MinusOperator extends BinaryOperator {
	static $Operators = array('-');

	function apply(array $operands) {
		return count($operands) < 2
			? -reset($operands)
			: parent::apply($operands);
	}
}

/*
	Class: LanguageConstruct
		언어 구조를 함수 객체로 제공한다. 직접 new 키워드를 이용하여 함수 객체를 생성하는 대신 <LanguageConstruct::getInstance()> 메서드를 통해 인스턴스를 받는다.

		다음의 키워드들을 지원한다.

		- 종료문: die, exit
		- 출력문: echo (null 반환), print (1 반환)
		- 포함문: include, include_once, require, require_once
		- eval
		- empty

		(start code)
		$empty = LanguageConstruct::getInstance('empty');
		assert($empty->call(null));
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>
*/
final class LanguageConstruct extends Functor {
	static $Keywords = array(
		'die', 'exit', 'echo', 'empty', 'eval',
		'include', 'include_once',
		'require', 'require_once',
		'print'
	);

	protected static $instances = array();
	public $keyword;

	/*
		Function: getInstance()
			언어 구조 함수자를 구한다. 반환되는 객체는 각 키워드마다 하나씩만 생성된다.

		Parameters:
			string $operator - 구할 키워드

		Returns:
			<LanguageConstruct> 인스턴스.
	*/
	static function getInstance($keyword) {
		if(!is_string($keyword))
			throw new InvalidArgumentException('Expected language construct keyword string');

		if(!in_array($keyword, self::$Keywords))
			throw new InvalidArgumentException("$keyword keyword is unsupported or invalid");

		if(!isset(self::$instances[$keyword]))
			self::$instances[$keyword] = new self($keyword);
		return self::$instances[$keyword];
	}

	protected function __construct($keyword) {
		$this->keyword = $keyword;
	}

	function apply(array $parameters) {
		if($this->keyword == 'echo') {
			foreach($parameters as $param)
				echo $param;
			return;
		}

		$params	= count($parameters)
				? '$parameters[0]'
				: '';

		eval("\$result = $this->keyword($params);");
		return $result;
	}
}


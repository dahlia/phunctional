<?php
/*
Title: PartialApplication

Dependencies:
	- <Functor>
*/
require_once dirname(__FILE__) . '/Functor.php';

/*
	Class: PartialApplication
		다른 함수의 특정 인자를 값으로 고정한 새로운 함수자를 만들어 사용한다.

		(start code)
		$getWords = new PartialApplication('split', array('[ \r\n\t\v]'));

		$string = trim(fgets(STDIN));
		assert(split('[ \r\n\t\v]', $string) == $getWords->call($string));
		(end)

	Extends:
		<Functor>

	Implements:
		- <Callable>

	See Also:
		- <http://en.wikipedia.org/wiki/Currying>
		- <http://www.python.org/dev/peps/pep-0309/>
		- <http://wwwasd.web.cern.ch/wwwasd/lhc++/RW/stdlibcr/bin_1899.htm>
		- <http://www.sgi.com/tech/stl/binder1st.html>
		- <http://www.sgi.com/tech/stl/binder2nd.html>
*/
class PartialApplication extends Functor {
	public $functor;
	public $bindingParameters;

	/*
		Constructor: __construct

		Parameters:
			functor $functor - 인자를 고정할 함수.
			array $bindingParameters - 고정할 인수들의 배열. 인자 순서를 배열 키로, 해당 순서의 인자에 고정할 값을 배열 값으로 받는다. 인자 값 셋을 받는 함수의 처음과 끝 인자를 'a'와 'c'로 고정하고 싶다면, array(0 => 'a', 2 => 'c')를 넣으면 된다.
	*/
	function __construct($functor, array $bindingParameters = array()) {
		$this->functor = Functor($functor);

		$keys = array_keys($bindingParameters);
		for($i = 0, $len = count($keys); $i < $len; ++$i) {
			if(!is_int($keys[$i]))
				unset($bindingParameters[$keys[$i]]);
		}

		$this->bindingParameters = $bindingParameters;
	}

	function apply(array $args) {
		$params = $this->bindingParameters;
		$length = count($params) + count($args);

		for($i = 0; $i < $length; ++$i) {
			if(!isset($params[$i]))
				$params[$i] = array_shift($args);
		}

		ksort($params);
		return $this->functor->apply($params);
	}
}

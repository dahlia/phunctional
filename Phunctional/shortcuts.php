<?php
/*
Title: Shortcuts
	다른 모듈들을 위한 단축 함수들. 함수들 각각은 *의존성(dependencies)이 만족되지 않을 경우 정의되지 않는다.* 따라서 이 파일은 다른 Phunctional의 모듈들을 포함(include)한 뒤, 최종적으로 포함해야 의도대로 작동한다.
*/

if(class_exists('PartialApplication') and !function_exists('bind1st')):

	/*
		Function: bind1st()
			<PartialApplication> 클래스를 위한 단축 함수. 특정 함수자의 첫번째 인자를 고정한 함수자를 구한다. new PartialApplication($functor, array(0 => $binding_arg))와 동일하다.

		Parameters:
			functor $functor - 인자를 고정할 함수자.
			$binding_arg - 첫번째 인자로 고정할 값.

		Returns:
			(PartialApplication) <PartialApplication> 인스턴스.

		Dependencies:
			- <PartialApplication>

		See Also:
			<http://www.sgi.com/tech/stl/binder1st.html>
	*/
	function bind1st($functor, $binding_arg) {
		return new PartialApplication($functor, array($binding_arg));
	}

	/*
		Function: bind2nd()
			<PartialApplication> 클래스를 위한 단축 함수. 특정 함수자의 두번째 인자를 고정한 함수자를 구한다. new PartialApplication($functor, array(1 => $binding_arg))와 동일하다.

		Parameters:
			functor $functor - 인자를 고정할 함수자.
			$binding_arg - 두번째 인자로 고정할 값.

		Returns:
			(PartialApplication) <PartialApplication> 인스턴스.

		Dependencies:
			- <PartialApplication>

		See Also:
			<http://www.sgi.com/tech/stl/binder2nd.html>
	*/
	function bind2nd($functor, $binding_arg) {
		return new PartialApplication($functor, array(1 => $binding_arg));
	}

endif;

if(class_exists('Filter') and !function_exists('filter')):

	/*
		Function: filter()
			<Filter> 클래스를 위한 단축 함수. 시그너쳐는 <Filter> 클래스 생성자와 동일하다.

		Parameters:
			functor $predicate - 인자로 들어온 값을 조건에 따라 논리값(bool)으로 반환하는 서술(predicate) 함수자.
			iterable|Filterable $iterable - 걸러낼 순차열.

		$predicate Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$predicate Returns:
			(bool) 원소를 포함시킬 경우 true, 제외시킬 경우 false.

		Returns:
			(Filter|iterable) <Filter> 인스턴스. $iterable이 <Filterable> 인스턴스일 경우 $iterable->filter($predicate)를 반환한다.

		Dependencies:
			- <Filter>
			- <Filterable>
	*/
	function filter($predicate, $iterable) {
		if($iterable instanceof Filterable)
			return Iterator($iterable->filter(Functor($predicate)));
		return new Filter($predicate, $iterable);
	}

endif;

if(class_exists('KeyIterator') and !function_exists('keys')):

	/*
		Function: keys()
			<KeyIterator> 클래스를 위한 단축 함수. 순차열의 키들을 반환한다.

		Parameters:
			iterable $iterable - 키를 취할 순차열.

		Returns:
			<KeyIterator> 인스턴스.

		Dependencies:
			- <KeyIterator>
	*/
	function keys($iterable) {
		return new KeyIterator($iterable);
	}

endif;

if(class_exists('PaddingIterator') and !function_exists('pad')):

	/*
		Function: pad()
			<PaddingIterator> 클래스를 위한 단축 함수. 시그너쳐는 <PaddingIterator> 클래스 생성자와 동일하다. 순차열을 밀어낸다.

		Parameters:
			iterable $iterable - 밀어낼 순차열.
			number $length - 늘어날 길이.
			mixed $value - 밀린 자리에 들어갈 값.

		Returns:
			<PaddingIterator> 인스턴스.

		Dependencies:
			- <PaddingIterator>
	*/
	function pad($iterable, $length, $value) {
		return new PaddingIterator($iterable, $length, $value);
	}

endif;

if(class_exists('Transformation') and !function_exists('map')):

	/*
		Function: map()
			<Transformation> 클래스를 위한 단축 함수. 시그너쳐는 <Transformation> 클래스의 생성자와 같다.

		Parameters:
			functor $functor - 원소에 적용할 함수자. 인자로 원소가 전달된다.
			iterable|Mappable $iterable - 걸러낼 순차열.

		$functor Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$functor Returns:
			새로 생성될 순차열의 원소.

		Returns:
			(Transformation|iterable) <Transformation> 인스턴스. $iterable이 <Mappable> 인스턴스일 경우 $iterable->map($functor)를 반환한다.

		Dependencies:
			- <Transformation>
			- <Mappable>
	*/
	function map($functor, $iterable) {
		if($iterable instanceof Mappable)
			return Iterator($iterable->map(Functor($functor)));
		return new Transformation($functor, $iterable);
	}

endif;

if(class_exists('Range') and !function_exists('xrange')):

	/*
		Function: xrange()
			<Range> 클래스를 위한 단축 함수. 시그너쳐는 <Range> 클래스의 생성자와 같다.

		Parameters:
			number|string $begin - 순차열의 첫 원소.
			number|string $end - 순차열의 마지막 원소. (순차열는 이 값을 포함하는 양개구간이다.)
			(number $step) - 연속적인 원소들의 증가값. 양수여야 한다.

		Returns:
			(Range) <Range> 인스턴스.

		Dependencies:
			- <Range>

		See Also:
			- <http://kr2.php.net/manual/en/function.range.php>
			- <http://docs.python.org/lib/built-in-funcs.html#l2h-80>
	*/
	function xrange($begin, $end, $step = 1) {
		return new Range($begin, $end, $step);
	}

endif;

if(class_exists('Zip') and !function_exists('zip')):

	/*
		Function: zip()
			<Zip> 클래스에 대한 단축 함수. 시그너쳐는 <Zip> 클래스의 생성자와 동일하다.

		Parameters:
			array $iterables - 원소를 묶을 평행한 순차열들.

		Returns:
			(Zip) <Zip> 인스턴스.

		Dependencies:
			- <Zip>
	*/
	function zip(array $iterables) {
		return new Zip($iterables);
	}

endif;

if(class_exists('Combination') and !function_exists('combine')):

	/*
		Function: combine()
			<Combination> 클래스에 대한 단축 함수. 시그너쳐는 <Combination> 클래스의 생성자와 동일하다.

		Parameters:
			iterable $keys - 키가 될 원소들의 순차열.
			iterable $values - 키에 대응하는 값이 될 원소들의 순차열.

		Returns:
			(Combination) <Combination> 인스턴스.

		Dependencies:
			- <Combination>
	*/
	function combine($keys, $values) {
		return new Combination($keys, $values);
	}

endif;

if(class_exists('Lambda') * !function_exists('fed') * !function_exists('def')):

	/*
		Function: def()
			<Lambda::begin()> 메서드에 대한 단축 함수. 시그너쳐와 동작은 <Lambda::begin()> 함수와 동일하다.

		Parameters:
			<Lambda::begin()> 참고.

		Returns:
			(bool) 항상 false.

		Dependencies:
			- <Lambda>
	*/
	function def() {
		$params = func_get_args();
		return Lambda::generate(debug_backtrace(), $params);
	}

	/*
		Function: fed()
			<Lambda::end()> 메서드에 대한 단축 함수. 동작은 <Lambda::end()> 함수와 동일하다. 삼항 연산자의 마지막 피연산자로 들어가며, 실제 람다 함수 객체를 반환한다.

		Returns:
			(Lambda) <def()> 혹은 <Lambda::begin()> 메서드에서 생성한 람다 함수.

		Dependencies:
			- <Lambda>
	*/
	function fed(array $context = array()) {
		return Lambda::end($context);
	}

endif;

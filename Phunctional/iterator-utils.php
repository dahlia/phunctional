<?php
/*
Package: Iterator Utilities
	반복자(Iterator)와 관련된 유틸리티 함수들을 제공한다.

See Also:
	<http://www.php.net/~helly/>
*/

/*
	Function: to_array()
		순차열 $iterable을 배열로 만든다. iterator_to_array()보다 융통성이 높아서, 배열을 넣을 경우 그대로를 반환한다.

		(start code)
		assert(to_array(new Range(1, 5)) == range(1, 5));
		(end)

	Parameters:
		iterable $iterable - 배열로 만들 순차열.

	Returns:
		(array) 만들어진 배열.

	Dependencies:
		- <Iterator>
*/
include_once dirname(__FILE__) . '/iterator-utils/to_array.php';

/*
	Function: count_if()
		순차열 $iterable의 원소 각각 $v와 그 키 $k에 대해, $functor->call($v, $k)가 참(true)인 원소의 갯수를 구한다. iterator_to_count(new Filter($iterable, $functor))와 같은 결과를 반환한다.

	Parameters:
		iterable $iterable - 헤아릴 순차열.
		functor $functor - 조건을 평가하는 함수자.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		(bool) 원소를 셀 경우 true, 세지 않을 경우 false.

	Returns:
		(int) 조건에 해당하는 원소의 갯수.

	Dependencies:
		- <Functor>
		- <Iterator>

	See Also:
		<http://www.sgi.com/tech/stl/count_if.html>
*/
include_once dirname(__FILE__) . '/iterator-utils/count_if.php';

/*
	Function: reduce()
		array_reduce()의 반복자 버전.
		$seq가 배열(array)이라고 가정할 때, reduce($f, $seq)는 $f(...($f($f($seq[0], $seq[1]), $seq[2]), ...), $seq[count($seq) - 1])과 같은 연산을 한다. 실제로는 $seq가 배열이 아니라 반복자여도 된다. 이것을 폰노이만 방식의 반복문을 사용한 코드로 표현하면 아래와 같다. (결과값은 $result.)

		(start code)
		foreach($seq as $x)
			$result = isset($result) ? $f->call($result, $x) : $x;
		(end code)

		예를 들어 reduce('+', $seq)는 순차열 $seq가 포함하는 원소들의 총합을 구한다.
		세번째 $initial로 초기값을 줄 경우, reduce($f, $seq, $init)는 $f(...($f($f($init, $seq[0]), $seq[1]), ...), $seq[count($seq) - 1])과 같은 연산을 한다. 이것 역시 반복문을 사용한 코드로 표현하면 아래와 같다. (결과값은 $result.)

		(start code)
		$result = $init;
		foreach($seq as $x)
			$result = $f->call($result, $x);
		(end)

	Parameters:
		functor $functor - 적용할 함수자.
		iterable|Injectable $iterable - 순회할 순차열. <Injectable> 인스턴스일 경우 $iterable->inject($functor)를 호출한다. ($initial이 주어질 경우 $iterable->injectWithInitialValue($functor, $initial)를 호출.)
		($initial) - 초기값. 없으면 $iterable의 첫번째 원소가 초기값이 된다.

	$functor Parameters:
		$context - 이전에 호출된 $functor가 반환한 값. 처음 호출될 경우 $initial 혹은 $iterable 순차열의 첫번째 원소가 전달된다.
		$current - 순차열 각각의 원소. $initial이 있을 경우 $iterable 순차열의 모든 원소가 호출할 때마다 하나씩 순서대로 전달되지만, $initial이 없을 경우에는 $iterable 순차열의 두번째 원소부터 차례대로 하나씩 전달된다.

	$functor Returns:
		다음 호출시 $context로 전달될 값. 마지막 호출시 반환된 값은 reduce() 함수가 최종적으로 반환하게 된다. $initial이 주어지지 않고 $iterable 순차열도 원소를 지니지 않고 있을 경우 null을 반환한다.

	Dependencies:
		- <Functor>
		- <Iterator>
		- <Injectable>

	See Also:
		- <http://www.php.net/manual/en/function.array-reduce.php>
		- <http://docs.python.org/lib/built-in-funcs.html#l2h-60>
*/
include_once dirname(__FILE__) . '/iterator-utils/reduce.php';

/*
	Function: detect()
		순차열 $iterable 안에서 원소 $e와 그 키 $k에 대해 $functor($e, $k)가 참(true)인 첫번째 원소에 대한 반복자 커서를 구한다. **이 함수는 원소 값 자체가 아니라, 원소에 대한 반복자 커서를 반환한다.** 값을 원할 경우 <detect_element()> 함수를 이용하면 된다.

	Parameters:
		iterable $iterable - 찾을 반복자.
		functor $functor - 조건을 평가할 함수자.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		(bool) 다음 원소를 검사하려면 false, 현재 원소를 구하는 것이라면 true.

	Returns:
		- (Iterator) 찾았을 경우, 해당 원소를 가리키는 반복자(Iterator) 인스턴스.
		- (null) 찾지 못했을 경우, null.

	Dependencies:
		- <Iterator>
*/
include_once dirname(__FILE__) . '/iterator-utils/detect.php';

/*
	Function: detect_element()
		순차열 $iterable 안에서 원소 $e와 그 키 $k에 대해 $functor($e, $k)가 참(true)인 첫번째 원소를 구한다. 값을 찾지 못할 경우 null을 반환하는데, **조건에 해당하는 값 자체가 null인 경우와 구분할 수 없다.** 따라서 순차열의 원소에 null이 포함되어 있을 있고, 찾을 조건에 null이 부합할 가능성이 있을 경우 <detect()> 함수를 이용한다.

	Parameters:
		iterable $iterable - 찾을 반복자.
		functor $functor - 조건을 평가할 함수자.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		(bool) 다음 원소를 검사하려면 false, 현재 원소를 구하는 것이라면 true.

	Returns:
		- (mixed) 찾은 원소 값.
		- (null) 찾지 못했을 경우, null.

	Dependencies:
		- <detect()>
*/
include_once dirname(__FILE__) . '/iterator-utils/detect_element.php';

/*
	Function: contains()
		순차열 $haystack 안에 $needle이 포함되어 있는지 확인한다. in_array()의 반복자 버전.

		(start code)
		assert(contains(1, new Range(1, 10)));
		assert(!contains(11, new Range(1, 10)));
		(end)

	Parameters:
		$needle - 찾을 값.
		iterable $haystack - 검색할 범위.
		(bool $strict) - true일 경우 타입까지 같은지 체크(===). 기본값 false는 내부적으로 == 연산자를 사용한다.

	Returns:
		(bool) $haystack 안에 $needle이 있으면 true, 없으면 false.

	See Also:
		<http://www.php.net/manual/en/function.in-array.php>
*/
include_once dirname(__FILE__) . '/iterator-utils/contains.php';

/*
	Function: except_from()
		순차열 $from에서 $exceptions의 원소들을 제외한 나머지의 순차열를 반환한다.

		(start code)
		$filter = except_from(new Range(5, 10), new Range(0, 20));
		foreach($filter as $x)
			echo "$x ";
		(end)

		위 코드는 아래와 같은 결과를 출력한다.

		(start code)
		0 1 2 3 4 11 12 13 14 15 16 17 18 19 20 
		(end)

	Parameters:
		iterable $exceptions - 제외할 원소들을 포함한 순차열.
		iterable $from - 원본 순차열.
		(bool $strict) - true일 경우 타입까지 같은지 체크(===). 기본값 false는 내부적으로 == 연산자를 사용한다.

	Returns:
		(Filter) 걸러낸 순차열. <Filter> 인스턴스.

	Dependencies:
		- <Filter>
		- <PartialApplication>
		- <CompositeFunction>
*/
include_once dirname(__FILE__) . '/iterator-utils/except_from.php';

/*
	Function: sum()
		인수로 받은 배열이나 반복자의 원소들의 합을 구한다. reduce('+', $iter)와 동일하게 작동한다. array_sum()의 반복자 버전.

		(start code)
		assert(sum(new Range(1, 10)) == 55);
		assert(sum(new Range(-5, 5), 'abs') == 30);
		(end)

	Parameters:
		iterable $iterable - 합을 구할 원소들을 포함한 배열이나 반복자.
		(functor $functor) - 함수자를 넣어줄 경우 $iter의 원소와 키 $v, $k 각각에 $functor->call($v, $k)한 결과를 원소로 취급한다. 즉, reduce('+', map($functor, $iter))와 동일하게 작동한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		총합에 포함될 값.

	Returns:
		원소들의 합.

	Dependencies:
		- <Functor>

	See Also:
		<http://www.php.net/manual/en/function.array-sum.php>
*/
include_once dirname(__FILE__) . '/iterator-utils/sum.php';

/*
	Function: product()
		인수로 받은 배열이나 반복자의 원소들의 곱을 구한다. reduce('*', $iter)와 동일하게 작동한다. array_product()의 반복자 버전.

		(start code)
		assert(3628800 == product(new Range(1, 10)));
		assert(0 == product(new Range(-5, 5), 'abs'));
		assert(3628800 == product(new Range(-10, -1), 'abs'));
		(end)

	Parameters:
		iterable $iterable - 곱을 구할 원소들을 포함한 배열이나 반복자.
		(functor $functor) - 함수자를 넣어줄 경우 $iter의 원소와 키 $v, $k 각각에 $functor->call($v, $k)한 결과를 원소로 취급한다. 즉, reduce('*', map($functor, $iter))와 동일하게 작동한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		곱하게 될 값.

	Returns:
		원소들의 곱.

	Dependencies:
		- <Functor>

	See Also:
		<http://www.php.net/manual/en/function.array-product.php>
*/
include_once dirname(__FILE__) . '/iterator-utils/product.php';


/*
	Function: max_element()
		순차열 $iterable의 원소 중 가장 큰 것을 찾는다.

		(start code)
		assert(max_element(new Range(1, 10)) == 10);
		assert(max_element(new Range(-6, 4), 'abs') == -6);
		(end)

	Parameters:
		iterable $iterable - 순차열.
		(functor $functor) - 함수자를 전달할 경우 원소 각각에 해당 함수자를 통과시킨 후 비교한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		비교될 값.

	Returns:
		순차열 $iterable의 원소들 중 가장 큰 값.

	Dependencies:
		- <Functor>

	See Also:
		<http://www.sgi.com/tech/stl/max_element.html>
*/
include_once dirname(__FILE__) . '/iterator-utils/max_element.php';

/*
	Function: min_element()
		순차열 $iterable의 원소 중 가장 작은 것을 찾는다.

		(start code)
		assert(min_element(new Range(1, 10)) == 1);
		assert(min_element(new Range(-5, 5), 'abs') == 0);
		(end)

	Parameters:
		iterable $iterable - 순차열.
		(functor $functor) - 함수자를 전달할 경우 원소 각각에 해당 함수자를 통과시킨 후 비교한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		비교될 값.

	Returns:
		순차열 $iterable의 원소들 중 가장 작은 값.

	Dependencies:
		- <Functor>

	See Also:
		<http://www.sgi.com/tech/stl/min_element.html>
*/
include_once dirname(__FILE__) . '/iterator-utils/min_element.php';

/*
	Function: any()
		순차열 $iterable의 원소들 중에 true가 있는지 확인한다. reduce('or', $iterble)와 동일하게 작동한다.

		(start code)
		assert(any(array(true, true, true, true)));
		assert(any(array(true, true, true, false)));
		assert(!any(array(false, false, false, false)));
		(end)

	Parameters:
		iterable $iterabble - 확인할 순차열.
		(functor $functor) - 함수자를 넣어줄 경우 $iter의 원소 $x 각각에 $functor->call($x)한 결과를 원소로 취급한다. 즉, reduce('or', map($functor, $iterble))와 동일하게 작동한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		(bool) 조건을 만족하면 true, 만족하지 않으면 false.

	Returns:
		(bool) $iterable의 원소들 중 true가 하나라도 있으면 true, 하나도 없으면 false.

	Dependencies:
		- <Functor>

	See Also:
		- <http://docs.python.org/lib/built-in-funcs.html#l2h-10>
		- <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:Array:some>
*/
include_once dirname(__FILE__) . '/iterator-utils/any.php';

/*
	Function: all()
		순차열 $iterable의 원소들이 모두 true인지 확인한다. reduce('and', $iterable)과 동일하게 작동한다.

		(start code)
		assert(all(array(true, true, true, true)));
		assert(!all(array(true, true, true, false)));
		(end)

	Parameters:
		iterable $iterable - 확인할 순차열.
		(functor $functor) - 함수자를 넣어줄 경우 $iter의 원소 $x 각각에 $functor->call($x)한 결과를 원소로 취급한다. 즉, reduce('and', map($functor, $iterable))와 동일하게 작동한다.

	$functor Parameters:
		$value - 원소 각각의 값
		number|string $key - 원소의 키

	$functor Returns:
		(bool) 조건을 만족하면 true, 만족하지 않으면 false.

	Returns:
		(bool) $iterable의 원소들이 모두 true일 때만 true, 하나라도 false가 있으면 false.

	Dependencies:
		- <Functor>

	See Also:
		- <http://docs.python.org/lib/built-in-funcs.html#l2h-9>
		- <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:Array:every>
*/
include_once dirname(__FILE__) . '/iterator-utils/all.php';

/*
	Function: merge()
		둘 이상의 순차열를 이어 붙인다. array_merge()의 반복자 버전.

		(start code)
		$merged = merge(new Range(1, 5), range(6, 10));
		assert($merged == range(1, 10));
		(end)

	Parameters:
		iterable $iterable... - 이어 붙일 둘 이상의 순차열.

	Dependencies:
		- <to_array()>

	Returns:
		순차열들을 순서대로 이어놓은 배열(array).
*/
include_once dirname(__FILE__) . '/iterator-utils/merge.php';

/*
	Function: groupby()
		순차열 원소 각각에 대해 $functor 함수자가 반환하는 기준값을 키로 하고, 같은 기준에 대한 원소들의 배열을 값으로 하는 연관 배열을 반환한다.

		(start code)
		assert(
			array(1 => array(1, -1), 2 => array(2, -2), 3 => array(3, -3))
			== groupby(array(1, 2, 3, -1, -2, -3), 'abs')
		);

		assert(
			array('a' => array('a', 'a'), 'b' => array('b'), 'c' => array('c'))
			== groupby('abca')
		);
		(end code)

	Parameters:
		iterable $iterable - 순차열.
		functor $functor - 기준값을 반환하는 함수자.

	Returns:
		(array) 연관 배열.
*/
include_once dirname(__FILE__) . '/iterator-utils/groupby.php';

/*
	Function: divide()
		순차열 $iterable를 $by개씩의 원소로 이루어진 배열들로 분배한다.

		(start code)
		$divided = divide(new Range(1, 10), 3);
		assert($divided[1] == array(3 => 4, 4 => 5, 5 => 6));
		assert($divided[3] == array(9 => 10));
		(end)

	Parameters:
		iterable $iterable - 분배할 순차열.
		int $by - 한 배열당 담을 원소 개수.

	Returns:
		(array) $by개씩의 원소를 담고 있는 배열들로 이루어진 배열(2차원 배열).
*/
include_once dirname(__FILE__) . '/iterator-utils/divide.php';

/*
	Function: compare()
		두 값을 받아 비교하여 -1, 0, 1 중 하나를 반환한다. usort()와 함께 사용하기 위한 함수.

	Parameters:
		$a - 비교할 값.
		$b - 비교할 값.

	Returns:
		- $a와 $b가 같을 경우 0
		- $a가 클 경우 1
		- $b가 클 경우 -1

	See Also:
		<http://kr.php.net/usort>
*/
include_once dirname(__FILE__) . '/iterator-utils/compare.php';

/*
	Function: at()
		배열 혹은 ArrayAccess 인스턴스에 대한 첨자 연산자 접근을 수행한다.

		(start code)
		$array = array('key' => 'value');
		assert('value' == at($array, 'key'));
		assert(is_null(at($array, 'non-defined-key')));
		assert(M_PI == at($array, 'non-defined-key', M_PI));
		(end)

	Parameters:
		array|ArrayAccess $array - 피연산자
		$key - 첨자 키
		$defaultValue - 해당 키가 존재하지 않을 경우 반환할 값. 기본값은 null.
*/
include_once dirname(__FILE__) . '/iterator-utils/at.php';

/*
	Function: to_str()
		반복자를 문자열로 바꾼다.

		(start code)
		$iterator = xrange(1, 5);
		assert('1, 2, 3, 4' == to_str(', ', $iterator));
		assert((string) $iterator == to_str($iterator));
		(end)

	Parameters:
		string $glue - (optional) 원소 사이에 들어갈 문자열
		iterable $iterable - 피연산자

	See Also:
		- <http://kr.php.net/manual/en/function.implode.php>
		- <http://kr.php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring>

	Dependencies:
		- <Iterator>
*/
include_once dirname(__FILE__) . '/iterator-utils/to_str.php';

/*
	Function: slice()
		array_slice(), substr()의 반복자 버전. 순차열의 부분만 잘라내서 얻는다.

	Parameters:
		iterable $iterable - 자를 순차열.
		int $offset - 자르기 시작할 위치. 0 이상의 정수만 가능. 생략할 경우 순차열의 처음부터 자른다.
		int $length - 자를 길이. 생략할 경우 순차열의 끝까지 자른다.

	Returns:
		$iterable이

		- 배열일 경우, 배열
		- 문자열일 경우, 문자열
		- 그 외의 반복자일 경우, LimitIterator 인스턴스

		를 반환한다.

	Dependencies:
		- <Iterator>
		- <to_str()>

	See Also:
		- <http://kr.php.net/manual/en/function.array-slice.php>
		- <http://kr.php.net/manual/en/function.substr.php>
		- <http://kr.php.net/manual/en/function.mb-substr.php>
		- <http://www.php.net/~helly/php/ext/spl/classLimitIterator.html>
*/
include_once dirname(__FILE__) . '/iterator-utils/slice.php';

/*
	Function: sorted()
		전달된 순차열을 정렬하여 반환한다. 정렬에서의 비교를 위한 함수자를 전달할 수도 있다.

	Parameters:
		iterable $iterable - 정렬할 순차열.
		(functor $comparer) - 정렬시 사용될 비교 함수.

	$comparer Parameters:
		$a - 정렬할 원소 값 하나.
		$b - 정렬할 원소 값 하나.

	$comparer Returns:
		주어진 $a, $b를 비교하여 정수를 반환한다. 0을 반환할 경우 두 값이 동등하다고 취급한다. 음수를 반환할 경우 $a가 앞으로 가고, 양수를 반환할 경우 $b가 앞으로 가게 된다.

	Returns:
		(array) 정렬된 배열.

	Dependencies:
		- <Iterator>
		- <to_array()>

	See Also:
		- <http://kr.php.net/manual/en/function.usort.php>
*/
include_once dirname(__FILE__) . '/iterator-utils/sorted.php';

/*
	Function: sorted_by_key()
		전달된 순차열을 주어진 기준에 따라 정렬하여 반환한다.

	Parameters:
		iterable $iterable - 정렬할 순차열.
		functor $key_getter - 정렬할 기준.
		(boolean $desc) - 내림차순으로 정렬할 경우 true를 전달한다. 기본값은 오름차순(true).

	$key_getter Parameters:
		$value - 정렬할 순차열의 특정 원소.

	$key_getter Returns:
		(mixed) 주어진 $value를 가지고서 정렬할 기준이 될 값을 구해서 반환한다.

	Returns:
		(array) 정렬된 배열.

	Dependencies:
		- <PartialApplication>
		- <CompositeFunction>
		- <sorted()>

	See Also:
		- <sorted()>
*/
include_once dirname(__FILE__) . '/iterator-utils/sorted_by_key.php';

/*
	Function: reversed()
		순차열을 받아 순서를 뒤집어서 반환한다.

	Parameters:
		iterable $iterable - 정렬할 순차열.

	Returns:
		(iterable) 뒤집힌 순차열

	Dependencies:
		- <to_array()>
		- <Reversible>
*/
include_once dirname(__FILE__) . '/iterator-utils/reversed.php';

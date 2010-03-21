<?php
/*
Title: Injectable

See Also:
	<reduce()>
*/

/*
	Interface: Injectable
		<reduce()> 함수가 제공하는 것과 같은 기능의 메서드를 포함한다. <reduce()> 함수는 인수로 전달된 순차열이 <Injectable> 인스턴스일 경우 자체적인 알고리즘을 사용하는 대신, 해당 인스턴스의 <Injectable->inject()> 혹은 <Injectable->injectWithInitialValue()> 메서드를 호출한 결과를 그대로 반환한다. 즉, 이 인터페이스는 특정 자료 구조에 대한 효율성을 보장하기 위한 것이다.

	Extends:
		- Traversable 
*/
interface Injectable extends Traversable {
	/*
		Method: inject()
			<reduce()> 알고리즘과 동일.

		Parameters:
			functor $functor - 적용할 함수자.

		$functor Parameters:
			$context - 이전에 호출된 $functor가 반환한 값. 처음 호출될 경우 순차열의 첫번째 원소가 전달된다.
			$current - 순차열 각각의 원소. $iterable 순차열의 두번째 원소부터 차례대로 하나씩 전달된다.

		$functor Returns:
			다음 호출시 $context로 전달될 값. 마지막 호출시 반환된 값은 <Injectable->inject()> 메서드가 최종적으로 반환하게 된다.
	*/
	function inject($functor);

	/*
		Method: injectWithInitialValue()
			<Injectable->inject()> 메서드와 동일하지만 초기값을 받는다. <reduce()> 함수의 마지막 인자 $initial 참고.

		Parameters:
			functor $functor - 적용할 함수자.
			$initial - 초기값.

		$functor Parameters:
			$context - 이전에 호출된 $functor가 반환한 값. 처음 호출될 경우 $initial 값이 전달된다.
			$current - 순차열 각각의 원소. $iterable 순차열 원소들이 차례대로 하나씩 전달된다.

		$functor Returns:
			다음 호출시 $context로 전달될 값. 마지막 호출시 반환된 값은 <Injectable->inject()> 메서드가 최종적으로 반환하게 된다.
	*/
	function injectWithInitialValue($functor, $initial);
}

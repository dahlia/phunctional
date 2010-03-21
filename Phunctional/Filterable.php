<?php
/*
Title: Filterable

See Also:
	<Filter>
*/

/*
	Interface: Filterable
		<Filter> 클래스가 제공하는 것과 같은 기능의 메서드를 포함한다. <Filter> 클래스는 생성자에 인수로 전달된 순차열이 <Filterable> 인스턴스일 경우 원소를 거르는 자체적인 알고리즘을 사용하는 대신, 해당 인스턴스의 <Filterable->filter()> 메서드에게 역할을 전적으로 위임한다. 즉, 이 인터페이스는 특정 자료 구조에 대한 효율성을 보장하기 위한 것이다.

	Extends:
		- Traversable 
*/
interface Filterable extends Traversable {
	/*
		Method: filter()
			순차열의 원소들에 대해 특정 조건을 검사하는 함수자를 만족하는 것들만 포함시키는 새로운 순차열를 반환한다. new Filter($predicate, $this)와 같은 순차열를 반환한다. (실제 반환되는 값의 형태는 다를 수 있다.)

		Parameters:
			functor $predicate - 인자로 원소 각각의 값과 키를 받고 조건에 따라 논리값(bool)으로 반환하는 서술자(predicate) 함수자.

		$predicate Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$predicate Returns:
			(bool) 원소를 포함시킬 경우 true, 제외시킬 경우 false.

		Returns:
			(iterable) 원소와 키 각각 $v, $k에 대해 $functor->call($v, $k)를 만족하는 원소만을 포함하는 순차열.
	*/
	function filter($predicate);
}


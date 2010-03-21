<?php
/*
Title: Mappable

See Also:
	<Transformation>
*/

/*
	Interface: Mappable
		<Transformation> 클래스가 제공하는 것과 같은 기능의 메서드를 포함한다. <Transformation> 클래스는 생성자에 인수로 전달된 순차열이 <Mappable> 인스턴스일 경우 원소 각각에 함수를 직접 적용하는 대신, 해당 인스턴스의 <Mappable->map()> 메서드에게 역할을 전적으로 위임한다. 즉, 이 인터페이스는 특정 자료 구조에 대한 효율성을 보장하기 위한 것이다.

	Extends:
		- Traversable 
*/
interface Mappable extends Traversable {
	/*
		Method: map()
			순차열 자신의 원소 각각에 특정 함수를 적용한 새로운 순차열를 반환한다. new Transformation($functor, $this)와 같은 순차열를 반환한다. (실제로 반환되는 값의 형태는 다를 수 있다.)

		Parameters:
			functor $functor - 원소에 적용할 함수자. 인자로 원소와 키가 전달된다.

		$functor Parameters:
			$value - 원소 각각의 값
			number|string $key - 원소의 키

		$functor Returns:
			새로 생성될 순차열의 원소.

		Returns:
			(iterable) 원소와 키 각각 $v, $k에 대해 $functor->call($v, $k)가 반환한 값들을 원소로하는 순차열.
	*/
	function map($functor);
}


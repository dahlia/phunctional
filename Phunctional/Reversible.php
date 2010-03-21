<?php
/*
Title: Reversible

See Also:
	<reversed()>
*/

/*
	Interface: Reversible
		<reversed()> 함수가 제공하는 것과 같은 기능의 메서드를 포함한다. <reversed()> 함수는 인수로 전달된 순차열이 <Reversible> 인스턴스일 경우 자체적인 알고리즘을 사용하는 대신, 해당 인스턴스의 <Reversible->reverse()> 메서드를 호출한 결과를 그대로 반환한다. 즉, 이 인터페이스는 특정 자료 구조에 대한 효율성을 보장하기 위한 것이다.

	Extends:
		- Traversable 
*/
interface Reversible extends Traversable {
	/*
		Method: inject()
			<reversed()> 함수와 의미적으로 동등한 동작을 한다.
	*/
	function reverse();
}

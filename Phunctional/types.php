<?php
/*
Title: Types
	PHP 원시 타입(primitive type)들의 타입 캐스팅의 함수 구현들. 콜백을 위해 사용한다.

See Also:
	<http://www.php.net/manual/en/language.types.type-juggling.php#language.types.typecasting>
*/

/*
	Function: bool()

	Parameters:
		$v - 아무 값.

	Returns:
		(bool) 논리형으로 캐스팅된 값.
*/
function bool($v) {
	return (bool) $v;
}

/*
	Function: boolean()

	Parameters:
		$v - 아무 값.

	Returns:
		(bool) 논리형으로 캐스팅된 값.

	See Also:
		<bool()>
*/
function boolean($v) {
	return (bool) $v;
}

/*
	Function: int()

	Parameters:
		$v - 아무 값.

	Returns:
		(int) 정수형으로 캐스팅된 값.

	See Also:
		<bool()>
*/
function int($v) {
	return (int) $v;
}

/*
	Function: integer()

	Parameters:
		$v - 아무 값.

	Returns:
		(int) 정수형으로 캐스팅된 값.

	See Also:
		<int()>
*/
function integer($v) {
	return (int) $v;
}

/*
	Function: float()

	Parameters:
		$v - 아무 값.

	Returns:
		(float) 부동소수점 타입으로 캐스팅된 값.
*/
function float($v) {
	return (real) $v;
}

/*
	Function: double()

	Parameters:
		$v - 아무 값.

	Returns:
		(float) 부동소수점 타입으로 캐스팅된 값.

	See Also:
		<float()>
*/
function double($v) {
	return (real) $v;
}

/*
	Function: real()

	Parameters:
		$v - 아무 값.

	Returns:
		(float) 부동소수점 타입으로 캐스팅된 값.

	See Also:
		<float()>
*/
function real($v) {
	return (real) $v;
}

/*
	Function: string()

	Parameters:
		$v - 아무 값.

	Returns:
		(string) 문자열로 캐스팅된 값.
*/
function string($v) {
	return (string) $v;
}

/*
	Function: array_()
		array는 키워드이기 때문에 함수 이름 뒤에 언더바가 붙는다.

	Parameters:
		$v - 아무 값.

	Returns:
		(array) 배열로 캐스팅된 값.
*/
function array_($v) {
	return (array) $v;
}

/*
	Function: object()

	Parameters:
		$v - 아무 값.

	Returns:
		(object) 객체로 캐스팅된 값.
*/
function object($v) {
	return (object) $v;
}

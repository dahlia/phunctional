<?php
/*
Title: Binding

Dependencies:
	- <Functor>
	- <PartialApplication>

See Also:
	- <PartialApplication>
*/
require_once dirname(__FILE__) . '/PartialApplication.php';

/*
	Class: Binding
		*이 클래스는 더이상 사용되지 않습니다. <PartialApplication> 클래스를 
사용하세요.*
*/
final class Binding extends PartialApplication {
	function __construct($functor, array $bindingParameters = array()) {
		trigger_error(
			'Binding is a deprecated class, and therefore use PartialApplication',
			E_USER_NOTICE
		);

		parent::__construct($functor, $bindingParameters);
	}
}


<?php
require_once dirname(__FILE__) . '/../Functor.php';

function min_element($iterable, $functor = null) {
	if(is_array($iterable) and is_null($functor))
		return count($iterable) ? min($iterable) : null;

	$iterable = Iterator($iterable);
	$functor = Functor($functor);

	foreach($iterable as $key => $element) {
		$current = $functor->call($element, $key);

		if(!isset($min) or $min > $current) {
			$min = $current;
			$min_element = $element;	
		}
	}

	return isset($min_element) ? $min_element : null;
}

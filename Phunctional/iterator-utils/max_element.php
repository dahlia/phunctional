<?php
require_once dirname(__FILE__) . '/../Functor.php';

function max_element($iterable, $functor = null) {
	if(is_array($iterable) and is_null($functor))
		return count($iterable) ? max($iterable) : null;

	$iterable = Iterator($iterable);
	$functor = Functor($functor);

	foreach($iterable as $key => $element) {
		$current = $functor->call($element, $key);

		if(!isset($max) or $max < $current) {
			$max = $current;
			$max_element = $element;	
		}
	}

	return isset($max_element) ? $max_element : null;
}

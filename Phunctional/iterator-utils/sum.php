<?php
require_once dirname(__FILE__) . '/../Functor.php';

function sum($iterable, $functor = null) {
	if(is_array($iterable) and is_null($functor))
		return array_sum($iterable);

	$functor = Functor($functor);

	foreach(Iterator($iterable) as $key => $value) {
		$value = $functor->call($value, $key);
		$sum = isset($sum) ? $sum + $value : $value;
	}

	return $sum;
}

<?php
require_once dirname(__FILE__) . '/../Iterator.php';
require_once dirname(__FILE__) . '/../Functor.php';

function groupby($iterable, $functor = null) {
	$functor = Functor($functor);
	$result = array();

	foreach(Iterator($iterable) as $value) {
		$key = $functor->call($value);

		if(!is_scalar($key))
			$key = (string) $key;

		if(isset($result[$key]))
			$result[$key][] = $value;
		else
			$result[$key] = array($value);
	}

	return $result;
}


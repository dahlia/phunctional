<?php
require_once dirname(__FILE__) . '/../Functor.php';
require_once dirname(__FILE__) . '/to_array.php';

function sorted($iterable) {
	$array = to_array($iterable);

	if(func_num_args() > 1) {
		$comparer = func_get_arg(1);
		uasort($array, Functor($comparer)->toCallback());
	}
	else
		asort($array);

	return $array;
}

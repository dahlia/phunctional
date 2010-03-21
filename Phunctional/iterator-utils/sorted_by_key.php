<?php
require_once dirname(__FILE__) . '/../PartialApplication.php';
require_once dirname(__FILE__) . '/../CompositeFunction.php';
require_once dirname(__FILE__) . '/sorted.php';

function sorted_by_key($iterable, $key_getter, $desc = false) {
	$compare = new PartialApplication(
		'compare_for_sort',
		array(2 => Functor($key_getter))
	);

	if($desc)
		$compare = new CompositeFunction($compare, '-');

	return sorted($iterable, $compare);
}

function compare_for_sort($a, $b, $key_getter = null) {
	if(!is_null($key_getter)) {
		$a = $key_getter->call($a);
		$b = $key_getter->call($b);
	}

	return $a == $b ? 0 : ($a > $b ? 1 : -1);
}

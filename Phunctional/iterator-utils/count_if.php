<?php
require_once dirname(__FILE__) . '/../Functor.php';
require_once dirname(__FILE__) . '/../Iterator.php';

function count_if($iterable, $predicate) {
	$predicate = Functor($predicate);
	$i = 0;

	foreach(Iterator($iterable) as $k => $v) if($predicate->call($v, $k))
		++$i;

	return $i;
}

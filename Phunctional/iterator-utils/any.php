<?php
require_once dirname(__FILE__) . '/../Functor.php';

function any($iterable, $predicate = null) {
	$iterable = Iterator($iterable);
	$predicate = Functor($predicate);

	foreach($iterable as $k => $v) {
		if($predicate ? $predicate->call($v, $k) : $v)
			return true;	
	}

	return false;
}

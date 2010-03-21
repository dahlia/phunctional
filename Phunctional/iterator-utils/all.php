<?php
require_once dirname(__FILE__) . '/../Functor.php';

function all($iterable, $predicate = null) {
	$iterable = Iterator($iterable);
	$predicate = Functor($predicate);

	foreach($iterable as $k => $v) {
		if($predicate ? !$predicate ->call($v, $k) : !$v)
			return false;	
	}

	return true;
}

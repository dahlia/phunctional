<?php
require_once dirname(__FILE__) . '/../Functor.php';

function detect($iterable, $predicate = null) {
	$predicate = Functor($predicate);
	$iterable = Iterator($iterable);

	for(; $iterable->valid(); $iterable->next()) {
		if($predicate->call($iterable->current(), $iterable->key()))
			return $iterable;
	}
}

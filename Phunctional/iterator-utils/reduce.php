<?php
require_once dirname(__FILE__) . '/../Functor.php';
require_once dirname(__FILE__) . '/../Iterator.php';
require_once dirname(__FILE__) . '/../Injectable.php';

function reduce($functor, $iterable) {
	$args = func_get_args();
	$functor = Functor($functor);

	if($iterable instanceof Injectable) {
		return count($args) > 2
			? $iterable->injectWithInitialValue($functor, $args[2])
			: $iterable->inject($functor);
	}

	$iterable = Iterator($iterable);

	if(count($args) > 2)
		$result = $args[2];
	else if($iterable->valid()) {
		$result = $iterable->current();
		$iterable->next();
	}
	else
		return null;

	for(; $iterable->valid(); $iterable->next())
		$result = $functor->call($result, $iterable->current());
	$iterable->rewind();

	return $result;
}

<?php
require_once dirname(__FILE__) . '/../Functor.php';

function product($iterable, $functor = null) {
	if(is_array($iterable) and is_null($functor))
		return array_product($iterable);

	$functor = Functor($functor);

	foreach($iterable as $key => $value) {
		$value = $functor->call($value, $key);
		$product = isset($product) ? $product * $value : $value;
	}

	return $product;
}

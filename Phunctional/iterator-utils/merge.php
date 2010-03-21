<?php
require_once dirname(__FILE__) . '/to_array.php';

function merge() {
	$iterables = func_get_args();
	foreach($iterables as &$iterable)
		$iterable = to_array($iterable);

	return call_user_func_array('array_merge', $iterables);
}

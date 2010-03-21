<?php

function divide($iterable, $by) {
	$iterable = Iterator($iterable);
	$result = array();
	$count = 0;
	$i = -1;

	foreach($iterable as $key => $value) {
		if($count % $by)
			$result[$i][$key] = $value;
		else
			$result[++$i] = array($key => $value);

		++$count;
	}

	return $result;
}

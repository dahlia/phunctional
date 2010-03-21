<?php
require_once dirname(__FILE__) . '/to_array.php';
require_once dirname(__FILE__) . '/../Iterator.php';
require_once dirname(__FILE__) . '/../Reversible.php';

function reversed($iterable) {
	if($iterable instanceof Reversible)
		return $iterable->reverse();
	else if(is_array($iterable))
		return array_reverse($iterable, true);
	else if(is_string($iterable)) {
		for($str = '', $i = mb_strlen($iterable) - 1; $i >= 0; --$i)
			$str .= mb_substr($iterable, $i, 1);
		return $str;
	}

	return array_reverse(to_array($iterable));
}

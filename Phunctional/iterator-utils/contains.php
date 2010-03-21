<?php

function contains($needle, $haystack, $strict = false) {
	if(is_array($haystack))
		return in_array($needle, $haystack, $strict);

	$haystack = Iterator($haystack);

	foreach($haystack as $value) {
		if($strict ? $needle === $value : $needle == $value)
			return true;
	}

	return false;
}

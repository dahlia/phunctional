<?php
require_once dirname(__FILE__) . '/detect.php';

function detect_element($iterable, $predicate = null) {
	$result = detect($iterable, $predicate);
	return is_null($result) ? null : $result->current();
}


<?php
require_once dirname(__FILE__) . '/../Iterator.php';

function to_array($iterable) {
	return is_array($iterable) ? $iterable : iterator_to_array(Iterator($iterable));
}

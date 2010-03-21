<?php
require_once dirname(__FILE__) . '/../Filter.php';
require_once dirname(__FILE__) . '/../PartialApplication.php';
require_once dirname(__FILE__) . '/../CompositeFunction.php';

function except_from($exceptions, $from, $strict = false) {
	return new Filter(new PartialApplication(
		new CompositeFunction('contains', '!'),
		array(1 => $exceptions, 2 => $strict)
	), $from);
}

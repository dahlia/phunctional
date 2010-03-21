<?php
require_once dirname(__FILE__) . '/../Iterator.php';

function to_str($iterable) {
	$params = func_get_args();

	if(count($params) < 2) {
		if(is_string($iterable))
			return $iterable;

		if(is_object($iterable) and method_exists($iterable, '__toString'))
			return $iterable->__toString();

		try {
			$str = '';
			foreach(Iterator($iterable) as $el)
				$str .= to_str($el);
			return $str;
		}
		catch(InvalidArgumentException $e) {
			switch(strtolower(gettype($iterable))) {
				case 'boolean':
				case 'integer':
				case 'double':
				case 'null':
					return (string) $iterable;

				case 'object':
					$type	= is_object($iterable)
							? get_class($iterable) : gettype($iterable);

					throw new InvalidArgumentException(
						"$type cannot be coverted to string"
					);
			}

			throw $e;
		}
	}

	$glue = to_str($params[0]);

	foreach(Iterator($params[1]) as $element) {
		if(isset($str))
			$str .= $glue . to_str($element);
		else
			$str = to_str($element);
	}

	return isset($str) ? $str : '';
}

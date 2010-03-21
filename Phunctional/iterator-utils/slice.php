<?php
require_once dirname(__FILE__) . '/../Iterator.php';
require_once dirname(__FILE__) . '/to_str.php';

function slice($iterable, $offset = 0, $length = null) {
	if(!is_null($length) and $length < 0) {
		throw new UnexpectedValueException(
			'$length must be a value greater than or equal 0'
		);
	}

	if(is_array($iterable)) {
		return array_slice(
			$iterable, $offset,
			is_null($length) ? count($iterable) : $length,
			true
		);
	}

	if(is_string($iterable)) {
		if(function_exists('mb_substr')) {
			return mb_substr(
				$iterable, $offset,
				is_null($length) ? mb_strlen($iterable) : $length
			);
		}

		return to_str('', new LimitIterator(
			Iterator($iterable), $offset,
			is_null($length) ? -1 : $length
		));
	}

	return new LimitIterator(
		Iterator($iterable), $offset,
		is_null($length) ? -1 : $length
	);
}


<?php
require_once 'PHPUnit/Extensions/ExceptionTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/Iterator.php';

abstract class SequenceTestCase extends PHPUnit_Extensions_ExceptionTestCase {
	private $__defaultString;

	function beginStringTest() {
		if(function_exists('mb_internal_encoding')) {
			$this->__defaultEncoding = mb_internal_encoding();
			mb_internal_encoding('UTF-8');
		}
	}

	function finishStringTest() {
		if(function_exists('mb_internal_encoding'))
			mb_internal_encoding($this->__defaultEncoding);
	}

	function assertSequenceEquals($expected, $sequence) {
		$parameters = func_get_args();

		for($i = 0; $i < 2; ++$i) {
			$parameters[$i] = is_array($parameters[$i])
							? $parameters[$i]
							: iterator_to_array(Iterator($parameters[$i]));
		}

		return call_user_func_array(array($this, 'assertEquals'), $parameters);
	}

	function assertSequenceEqualsWithIndex($expected, $sequence) {
		for($expected = Iterator($expected), $sequence = Iterator($sequence);
			$expected->valid() or $sequence->valid();
			$expected->next(), $sequence->next()
		) {
			if(!$expected->valid() or !$sequence->valid()
				or $expected->current() !== $sequence->current()
				or $expected->key() !== $sequence->key()) {
				$this->fail();
			}
		}
	}

	function assertSequenceContains($item, $sequence, $message = null) {
		foreach(Iterator($sequence) as $value) {
			if($item == $value)
				return true;
		}

		if(is_null($message))
			$this->fail();
		$this->fail($message);
	}
}


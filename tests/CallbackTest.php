<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

function functionForTest($a, $b) {
	static $value = null;
	if(is_null($value))
		$value = rand();
	return $value + $a * $b;
}

final class CallbackTest extends PHPUnit_Framework_TestCase {
	protected $a, $b;

	function __construct() {
		$this->a = rand();
		$this->b = rand();
	}

	function testCallbackToFunction() {
		$function = 'functionForTest';
		$callback = new Callback($function);

		$this->assertEquals(
			$function($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	function methodForTest($a, $b) {
		return functionForTest($a, $b);
	}

	function testCallbackToMethod() {
		$method = 'methodForTest';
		$callback = new Callback($this, $method);

		$this->assertEquals(
			$this->$method($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	function testCallbackToMethodByLegacyForm() {
		$method = 'methodForTest';
		$callback = new Callback(array($this, $method));

		$this->assertEquals(
			$this->$method($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	static function staticMethodForTest($a, $b) {
		return functionForTest($a, $b);
	}

	function testCallbackToStaticMethod() {
		$callback = new Callback(__CLASS__, 'staticMethodForTest');

		$this->assertEquals(
			self::staticMethodForTest($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	function testCallbackToStaticMethodByLegacyForm() {
		$callback = new Callback(array(__CLASS__, 'staticMethodForTest'));

		$this->assertEquals(
			self::staticMethodForTest($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	function testCallbackToStaticMethodByString() {
		$callback = new Callback(__CLASS__ . '::staticMethodForTest');

		$this->assertEquals(
			self::staticMethodForTest($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}

	static function nn($a, $b) {
		return functionForTest($a, $b);
	}

	function testCallbackToStaticMethodByStringStartsWithN() {
		$callback = new Callback(__CLASS__ . '::nn');

		$this->assertEquals(
			self::nn($this->a, $this->b),
			$callback->call($this->a, $this->b)
		);
	}
}


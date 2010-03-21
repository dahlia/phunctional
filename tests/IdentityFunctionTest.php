<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class IdentityFunctionTest extends PHPUnit_Framework_TestCase {
	function testSingleton() {
		$constructor = new ReflectionMethod('IdentityFunction', '__construct');

		$this->assertTrue($constructor->isConstructor());
		$this->assertFalse($constructor->isPublic());
	}

	function testGetInstance() {
		$this->assertSame(
			IdentityFunction::getInstance(),
			IdentityFunction::getInstance()
		);
	}

	function testReturnValue() {
		$identityFunction = IdentityFunction::getInstance();

		for($i = 0; $i < 10; ++$i) {
			$expected = rand();
			$value = $identityFunction->call($expected);
			$this->assertEquals($expected, $value);
		}
	}
}


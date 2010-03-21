<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';

final class OperatorTest extends PHPUnit_Framework_TestCase {
	function testGetInstanceWithInvalidOperator() {
		try {
			Operator::getInstance('invalid-operator');
		}
		catch(InvalidArgumentException $e) {
			return;
		}

		$this->fail();
	}

	function testGetInstance() {
		$this->assertEquals('+', Operator::getInstance('+')->operator);
	}

	function testSingleton() {
		$this->assertSame(
			Operator::getInstance('+'),
			Operator::getInstance('+')
		);
	}
}


<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Currying.php';

final class CurryingTest extends PHPUnit_Framework_TestCase {
	protected $level = null;

	function testSuperclass() {
		$this->assertTrue(is_subclass_of('Currying', 'PartialApplication'));
	}

	function errorHandler($level) {
		$this->errored = $level;
	}

	function testNotice() {
		set_error_handler(array($this, 'errorHandler'));
		new Currying('substr');
		restore_error_handler();

		$this->assertEquals(E_USER_NOTICE, $this->errored);
	}
}


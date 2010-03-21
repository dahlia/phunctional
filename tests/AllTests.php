<?php
error_reporting(E_ALL);

define('PHUNCTIONAL_TESTS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

final class AllTests {
	static function suite() {
		$tests = dir(PHUNCTIONAL_TESTS_PATH);
		$suite = new PHPUnit_Framework_TestSuite('Phunctional Framework');

		while($file = $tests->read()) if($class = self::isTestFile($file)) {
			include_once PHUNCTIONAL_TESTS_PATH . $file;
			$suite->addTestSuite($class);
		}

		return $suite;
	}

	protected static function isTestFile($file) {
		if(!eregi('^([a-z][a-z0-9_]*Test)\.php$', $file, $i))
			return false;
		else if(PHUNCTIONAL_TESTS_PATH . $file == __FILE__)
			return false;

		return $i[1];
	}
}


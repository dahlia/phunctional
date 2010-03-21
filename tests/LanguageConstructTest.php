<?php
require_once 'PHPUnit/Extensions/OutputTestCase.php';
require_once dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';

final class LanguageConstructTest extends PHPUnit_Extensions_OutputTestCase {
	function testGetInstanceWithInvalidKeyword() {
		try {
			LanguageConstruct::getInstance('invalid-keyword');
		}
		catch(InvalidArgumentException $e) {
			return;
		}

		$this->fail();
	}

	function testGetIntance() {
		$this->assertEquals('echo', LanguageConstruct::getInstance('echo')->keyword);
	}

	function testSingleton() {
		$this->assertSame(
			LanguageConstruct::getInstance('echo'),
			LanguageConstruct::getInstance('echo')
		);
	}

	protected function runCode($code) {
		if(substr(PHP_OS, 0, 3) == 'WIN') {
			$this->markTestSkipped();
			return;
		}

		$operatorPath = dirname(__FILE__) . '/../Phunctional/LanguageConstruct.php';
		$code = 'require_once ' . var_export($operatorPath, true) . ';' . trim($code);
		$output = exec('php -n -r ' . escapeshellarg($code), $o, $r);

		if($r === 127)
			$this->markTestSkipped();

		return $output;
	}

	function testExit() {
		$testCode = '
			$exit = LanguageConstruct::getInstance("exit");
			$exit->call();
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('', $result);
	}

	function testExitWithMessage() {
		$testCode = '
			$exit = LanguageConstruct::getInstance("exit");
			$exit->call("with message");
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('with message', $result);
	}

	function testDie() {
		$testCode = '
			$die = LanguageConstruct::getInstance("die");
			$die->call();
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('', $result);
	}

	function testDieWithMessage() {
		$testCode = '
			$die = LanguageConstruct::getInstance("die");
			$die->call("with message");
			echo "failed";
		';

		$result = $this->runCode($testCode);
		$this->assertEquals('with message', $result);
	}

	function testEcho() {
		$echo = LanguageConstruct::getInstance('echo');
		$value = rand();

		$this->expectOutputString((string) $value);
		$returns = $echo->call($value);

		$this->assertEquals(null, $returns);
	}

	function testEchoWithManyParameters() {
		$echo = LanguageConstruct::getInstance('echo');

		$this->expectOutputString('1234');
		$returns = $echo->call(1, 2, 3, 4);

		$this->assertEquals(null, $returns);
	}

	function testPrint() {
		$print = LanguageConstruct::getInstance('print');
		$value = rand();

		$this->expectOutputString((string) $value);
		$returns = $print->call($value);

		$this->assertEquals(1, $returns);
	}

	function testEmpty() {
		$empty = LanguageConstruct::getInstance('empty');

		$this->assertTrue($empty->call(''));
		$this->assertTrue($empty->call(0));
		$this->assertTrue($empty->call('0'));
		$this->assertTrue($empty->call(null));
		$this->assertTrue($empty->call(false));
		$this->assertTrue($empty->call(array()));

		$this->assertFalse($empty->call('string'));
		$this->assertFalse($empty->call(1));
		$this->assertFalse($empty->call('1'));
		$this->assertFalse($empty->call(true));
		$this->assertFalse($empty->call(array(1)));
	}
}


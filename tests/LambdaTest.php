<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Lambda.php';

final class LambdaTest extends PHPUnit_Framework_TestCase {
	function testPlus() {
		$lambda = Lambda::begin(&$a, &$b)? $a + $b :Lambda::end();
		$this->assertEquals(3, $lambda->call(1, 2));
	}

	function testCall() {
		$lambda = Lambda::begin(&$a)? substr($a, 1, -1) :Lambda::end();
		$string = "Le peu de temps que j'ai eu a ete cause de l'un et de l'autre.";
		$value = $lambda->call($string);
		$expected = substr($string, 1, -1);

		$this->assertEquals($expected, $value);
	}

	function testExplicitContextClosing() {
		$lambda = Lambda::begin()? $a :Lambda::end();
		$lambda->context['a'] = 'virtual var';

		$this->assertEquals('virtual var', $lambda->call());
		$this->assertEquals('virtual var', $lambda->apply(array()));
	}

	function testStaticVariableClosing() {
		static $a = 'static var';
		$lambda = Lambda::begin()? $a :Lambda::end();

		$this->assertEquals('static var', $lambda->call());
		$this->assertEquals('static var', $lambda->apply(array()));
	}

	function testGlobalVariableClosing() {
		exec(
			'php -n ' . dirname(__FILE__) . '/_LambdaGlobalClosureTest.php',
			$o, $r
		);

		if($r === 127)
			$this->markTestSkipped();

		$this->assertEquals(array('global var', 'global var'), $o);
	}

	function testTrinaryOperatorContainsSpace() {
		$lambda = Lambda::begin(&$a) ?
			"test" . $a
		: Lambda::end();

		$this->assertEquals('testa', $lambda->call('a'));
		$this->assertEquals('testa', $lambda->apply(array('a')));
	}

	function testTrinaryOperatorDoesNotContainAnySpace() {
		$lambda = Lambda::begin(&$a)?"test".$a:Lambda::end();
		$this->assertEquals('testa', $lambda->call('a'));
		$this->assertEquals('testa', $lambda->apply(array('a')));
	}

	function testStringLiteral() {
		$lambda = Lambda::begin()? 'test: // is not php comment.' :Lambda::end();
		$this->assertEquals('test: // is not php comment.', $lambda->call());
	}

	function testTwoLambdaWithSameLine() {
		$this->markTestSkipped('Impossible test.');

		$a = Lambda::begin()? 1 :Lambda::end(); $b = Lambda::begin()? 2 :Lambda::end();

		$this->assertEquals(1, $a->call());
		$this->assertEquals(2, $a->call());
	}

	function testClose() {
		$lambda = Lambda::begin()? $a :Lambda::end();
		$lambdaWithClosing = $lambda->close(array('a' => 'closed'));

		$this->assertNotSame($lambda, $lambdaWithClosing);
		$this->assertEquals('closed', $lambdaWithClosing->call());
	}

	function testEndWithClosing() {
		$lambda = Lambda::begin()? $a :Lambda::end(array('a' => 'closed'));
		$this->assertEquals('closed', $lambda->call());
	}
}


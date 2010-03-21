<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/RawLambda.php';

final class RawLambdaTest extends PHPUnit_Framework_TestCase {
	function testPlus() {
		$lambda = new RawLambda('$0 + $1');
		$this->assertEquals(3, $lambda->call(1, 2));
	}

	function testCall() {
		$lambda = new RawLambda('substr($0, 1, -1)');
		$string = "Le peu de temps que j'ai eu a ete cause de l'un et de l'autre.";
		$value = $lambda->call($string);
		$expected = substr($string, 1, -1);

		$this->assertEquals($expected, $value);
	}

	function testUniqueFunctionName() {
		$a = new RawLambda('$0 + $1');
		$b = new RawLambda('$0 + $1');

		$this->assertEquals($a->toCallback(), $b->toCallback());
	}
}


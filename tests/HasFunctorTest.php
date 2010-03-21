<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class HasFunctorImplementation implements HasFunctor {
	function __construct($functor) {
		$this->functor = $functor;
	}

	function toFunctor() {
		return $this->functor;
	}
}

final class HasFunctorTest extends PHPUnit_Framework_TestCase {
	function testLegacyCallback() {
		$hasFunctor = new HasFunctorImplementation('substr');
		$functor = Functor($hasFunctor);
		$callback = new Callback('substr');

		$this->assertEquals($callback, $functor);
	}

	function testRecursiveHasFunctor() {
		$metaHasFunctor = new HasFunctorImplementation('substr');
		$hasFunctor = new HasFunctorImplementation($metaHasFunctor);
		$functor = Functor($hasFunctor);
		$callback = new Callback('substr');

		$this->assertEquals($callback, $functor);
	}

	function testCallable() {
		$callable = new ArrayAccessor(0);
		$hasFunctor = new HasFunctorImplementation($callable);
		$functor = Functor($hasFunctor);

		$this->assertEquals($callable, $functor);
	}
}


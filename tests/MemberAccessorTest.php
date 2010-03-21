<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/Functor.php';

final class MemberAccessorTest extends PHPUnit_Framework_TestCase {
	public $member;
	protected $fixture;

	function __construct() {
		$this->member = 'member';
		$this->fixture = new MemberAccessor($this->member);
	}

	function testInstantiate() {
		$this->assertEquals($this->member, $this->fixture->member);
	}

	function testCall() {
		$result = $this->fixture->call($this);
		$this->assertEquals($this->member, $result);
	}
}


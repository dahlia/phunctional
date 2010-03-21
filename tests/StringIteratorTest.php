<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Phunctional/StringIterator.php';

final class StringIteratorTest extends PHPUnit_Framework_TestCase {
	protected $stringIterator;

	function __construct() {
		$this->stringIterator = new StringIterator(
			"\xea\xb0\x80\xeb\x82\x98\xeb\x8b\xa4abcd\n",
			'UTF-8'
		);
	}

	function testInterface() {
		$this->assertType('Iterator', $this->stringIterator);
	}

	function testIterator() {
		$expect = array(
			0 => "\xea\xb0\x80", 3 => "\xeb\x82\x98", 6 => "\xeb\x8b\xa4",
			9 => 'a', 'b', 'c', 'd', "\n"			
		);

		$this->assertEquals($expect, iterator_to_array($this->stringIterator));
	}

	function testString() {
		$this->assertEquals(
			"\xea\xb0\x80\xeb\x82\x98\xeb\x8b\xa4abcd\n",
			$this->stringIterator->string
		);
	}

	function testDefaultEncoding() {
		$create = create_function('', 'return new StringIterator("");');

		if(function_exists('mb_internal_encoding')) {
			$default = mb_internal_encoding();
			$this->assertAttributeEquals($default, 'encoding', $create());

			foreach(array('EUC-KR', 'EUC-JP', 'SJIS', 'BIG-5') as $enc) {
				mb_internal_encoding($enc);
				$this->assertAttributeEquals($enc, 'encoding', $create());
			}

			mb_internal_encoding('UTF-8');
		}

		$this->assertRegExp(
			'/^\\s*utf[_-]8?\\s*$/i',
			$create()->encoding
		);

		if(function_exists('mb_internal_encoding'))
			mb_internal_encoding($default);
	}

	function testIterateEachByte() {
		$stringIterator = new StringIterator(
			"\xea\xb0\x80\xeb\x82\x98\xeb\x8b\xa4abcd\n",
			null
		);

		$expected = array(
			"\xea", "\xb0", "\x80", "\xeb", "\x82", "\x98", "\xeb", "\x8b",
			"\xa4", 'a', 'b', 'c', 'd', "\n"
		);

		$this->assertNull($stringIterator->encoding);
		$this->assertEquals($expected, iterator_to_array($stringIterator));
	}

	function testInvalidArgument() {
		$invalidValues = array(
			1, 3.14, true, false, null,
			array(), array(1, 2), array("string"),
			new ArrayIterator(array(1, 2, 3))
		);

		foreach($invalidValues as $value) {
			try {
				new StringIterator($value);
			}
			catch(InvalidArgumentException $e) {
				continue;
			}

			$this->fail();
		}
	}

	function testInvalidEncoding() {
		try {
			new StringIterator('a', 'invalid encoding');
		}
		catch(InvalidArgumentException $e) {
			return;
		}

		$this->fail();
	}

	function testCopyStringIterator() {
		try {
			new StringIterator($this->stringIterator);
		}
		catch(InvalidArgumentException $e) {
			$this->fail();
		}
	}

	function testToString() {
		$this->assertEquals(
			$this->stringIterator->string,
			$this->stringIterator->__toString()
		);
	}

	function testMultibyteStringSupport() {
		if(!function_exists('mb_list_encodings'))
			$this->markTestSkipped('Need mbstring extension');

		$strings = array(
			# encoding type => encoded string
			'EUC-KR' => "Multibyte \xb9\xae\xc0\xda\xbf\xad",
			'EUC-JP' => "Multibyte \xca\xb8\xbb\xfa\xce\xf3",
			'BIG-5' => "Multibyte \xa6r\xb2\xc5\xa6\xea",
			'SJIS' => "Multibyte \x95\xb6\x8e\x9a\x97\xf1"
		);

		foreach($strings as $encoding => $string) {
			try {
				$length = 0;
				foreach(new StringIterator($string, $encoding) as $char)
					++$length;
				$this->assertEquals(13, $length);
			}
			catch(InvalidArgumentException $e) {
				if(!eregi(' is not supported encoding$', $e->getMessage()))
					throw $e;
			}
		}
	}
}


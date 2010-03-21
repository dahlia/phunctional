<?php
/*
Title: StringIterator
	문자열 반복자.
*/

/*
	Class: StringIterator
		StringIterator는 문자열 반복자이다. 인코딩에 따라 문자를 단위로 순회한다. 각 원소의 키는 해당 문자의 바이트 오프셋이 된다.

		(start code)
		$striter = new StringIterator("가나다abc");
		$chars = iterator_to_array($striter);
		$expected = array(0 => '가', 3 => '나', 6 => '다', 9 => 'a', 'b', 'c');
		assert($expected == $chars);
		(end)

		생성자의 두번째 인자로 인코딩 이름을 문자열로 받는데, 기본값은 'UTF-8'이다. null을 전달하면 바이트열로 취급하게 된다.

		(start code)
		$striter = new StringIterator("가나다abc");
		$chars = iterator_to_array($striter);

		$expected = array(
			"\xea", "\xb0", "\x80", "\xeb", "\x82", "\x98", "\xeb", "\x8b", "\xa4",
			'a', 'b', 'c', 'd', "\n"
		);

		assert($expected == $chars);
		(end)

	Supported Encodings:
		'UTF-8' - 현재로서는 UTF-8 인코딩만 지원한다.
		null - 바이트열로 취급.
*/
class StringIterator implements Iterator {
	public $string;
	public $encoding;
	public $bytesLength;
	protected $offset = 0;

	/*
		Constructor: __construct

		Parameters:
			string $string - 순회할 문자열.
			string $encoding - 문자열의 인코딩. null을 전달할 경우 바이트 단위로 순회한다.
	*/
	function __construct($string) {
		if(func_num_args() < 2) {
			$encoding	= function_exists('mb_internal_encoding')
						? mb_internal_encoding()
						: 'UTF-8';
		}
		else
			$encoding = func_get_arg(1);

		if($string instanceof self) {
			$encoding = $string->encoding;
			$this->offset = $string->offset;
			$string = $string->string;
		}

		if(!is_string($string))
			throw new InvalidArgumentException('Expected string');

		if(!is_null($encoding))
			$encoding = trim($encoding);

		if(eregi('^utf[-_. ]*8$', $encoding))
			$encoding = 'UTF-8';
		else {
			$ok = is_null($encoding);

			if(function_exists('mb_list_encodings'))
				$ok = ($ok or in_array($encoding, mb_list_encodings()));

			if(!$ok) {
				throw new InvalidArgumentException(
					"$encoding is not supported encoding"
				);
			}
		}

		$this->string = $string;
		$this->encoding = $encoding;
		$this->bytesLength = strlen($string);
	}

	protected function getLength() {
		if(is_null($this->encoding) or eregi('^8[-_. ]*bit$', $this->encoding))
			return 1;

		if(function_exists('mb_substr')) {
			return mb_strlen(
				mb_substr(
					mb_substr(
						$this->string, $this->offset,
						mb_strlen($this->string, '8bit'), '8bit'
					),
					0, 1, $this->encoding
				), '8bit'
			);
		}

		$c = ord($this->string[$this->offset]);
		return $c < 0x80 ? 1 : ($c < 0xE0 ? 2 : ($c < 0xF0 ? 3 : 4));
	}

	function valid() {
		return $this->offset < $this->bytesLength;
	}

	function next() {
		if(!$this->valid())
			return false;

		$this->offset += $this->getLength();
	}

	function current() {
		return ($length = $this->getLength()) > 1
			? substr($this->string, $this->offset, $length)
			: $this->string[$this->offset];
	}

	function key() {
		return $this->offset;
	}

	function rewind() {
		$this->offset = 0;
	}

	function __toString() {
		return $this->string;
	}
}


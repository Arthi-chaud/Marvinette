<?php

require_once 'src/Exception/InvalidTestFolderException.php';
use PHPUnit\Framework\TestCase;

class InvalidTestFolderExceptionTest extends TestCase
{
	public function testCatchEofException(): void
	{
		$catched = false;
		try {
			throw new InvalidTestFolderException();
		} catch (InvalidTestFolderException $e) {
			$catched = true;
		}
		$this->assertTrue($catched);
	}

	public function testCatchEofExceptionWhenMarvinetteExpected(): void
	{
		$catched = false;
		try {
			throw new InvalidTestFolderException();
		} catch (MarvinetteException $e) {
			$catched = true;
		}
		$this->assertTrue($catched);
	}

	public function testCatchEofExceptionWhenExceptionExpected(): void
	{
		$catched = false;
		try {
			throw new InvalidTestFolderException();
		} catch (Exception $e) {
			$catched = true;
		}
		$this->assertTrue($catched);
	}
}
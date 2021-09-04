<?php

require_once 'tests/MarvinetteTestCase.php';

use PHPUnit\Framework\TestCase;
require_once 'tests/MarvinetteTestCase.php';
require_once 'src/Exception/EndOfFileException.php';
require_once 'src/Project.php';
require_once 'src/Utils/UserInput.php';

final class UserInputTest extends MarvinetteTestCase
{
	const stdinClone = TmpFileFolder . DIRECTORY_SEPARATOR . 'marvinetteTest';

	public function testGetUserInput(): void
	{
		$eof = false;
		$lineCount = 0;
		$expectedLineCount = 3;
		$lines = [];
		$expectedLines = ['Hello', 'World', 'Marvin'];
		$this->defineStdin($expectedLines);
		while (!$eof) {
			try {
				$line = UserInput::getUserLine();
				$lineCount++;
				$lines[] = $line;
			} catch (EndOfFileException $e) {
				$eof = true;
			}
		}
		$this->assertEquals($expectedLineCount, $lineCount);
		$this->assertEquals($expectedLines, $lines);
	}

	public function testGetOptionFromUserInput(): void
	{
		$lines = ['Hello', 'World', 'Marvin'];
		$expected = ['Marvin'];
		$this->defineStdin($lines);
		$this->expectOutputString("Enter Option\nEnter Option\nEnter Option\n");

		$entered = UserInput::getOption(function() {
			echo "Enter Option\n";
		}, $expected);
		$this->assertEquals($entered, $expected[0]);
	}

	public function testGetOptionFromUserInputWhenEmptyStream(): void
	{
		$lines = ['Hello', 'World', 'Marvin', 'BYE'];
		$expected = ['TROLOLOL'];
		$this->defineStdin($lines);
		$this->expectOutputString("Enter Option\nEnter Option\nEnter Option\nEnter Option\nEnter Option\n\n");
		$catched = false;
		try {
			UserInput::getOption(function() {
				echo "Enter Option\n";
			}, $expected);
		} catch (EndOfFileException $e) {
			$catched = true;
		}
		$this->assertTrue($catched);
	}

	public function testGetYesNoOption(): void
	{
		$this->hideStdout();
		$fileLines = ['Hello', 'Trololol', 'Y', "END"];
		$this->defineStdin($fileLines);
		$answer = UserInput::getYesNoOption("", "", Display\Color::Black);
		$this->assertEquals($answer, true);
	}
}
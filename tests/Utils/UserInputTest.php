<?php

use PHPUnit\Framework\TestCase;
require_once 'tests/TestUtils.php';
require_once 'src/Exception/EndOfFileException.php';
require_once 'src/Project.php';
require_once 'src/Utils/UserInput.php';

final class UserInputTest extends TestCase
{
    const stdinClone = '/tmp/marvinetteTest';

    public function testGetUserInput(): void
    {
        $eof = false;
        $lineCount = 0;
        $expectedLineCount = 3;
        $lines = [];
        $expectedLines = ['Hello', 'World', 'Marvin'];
        defineStdinClone($expectedLines);
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
        defineStdinClone($lines);
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
        defineStdinClone($lines);
        $this->expectOutputString("Enter Option\nEnter Option\nEnter Option\nEnter Option\nEnter Option\n");

        $this->expectException(EndOfFileException::class);
        UserInput::getOption(function() {
            echo "Enter Option\n";
        }, $expected);
    }
}
<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;

use function PHPUnit\Framework\assertEquals;

require_once 'src/Utils/CommandLine.php';

final class CommandLineTest extends TestCase
{
	public function testGetOptions(): void
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		  );
		global $argv;
		global $argc;
		$GLOBALS['argv'] = ["./hello", "--argc", "-a", "-v", "--argv"];
		$GLOBALS['argc'] = count($GLOBALS['argv']);
		$availableOptions = ["a", "c", "b", "v", "argc", "argv", "argb"];
		$actualOptions = CommandLine::getArguments($availableOptions);
		$this->assertContains("argv", $actualOptions);
		$this->assertContains("argc", $actualOptions);
		$this->assertContains("v", $actualOptions);
		$this->assertContains("a", $actualOptions);
	}
}
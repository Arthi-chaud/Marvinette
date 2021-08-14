<?php

use PHPUnit\Framework\TestCase;
require_once 'tests/TestUtils.php';
require_once 'src/Utils/UserInterface.php';

final class UserInterfaceTest extends TestCase
{
	public function setUp(): void
	{
		UserInterface::$titlesStack = [];
	}

	public function testSetTitle(): void
	{
		$title = "Hello World";
		UserInterface::setTitle($title);

		$this->assertEquals([$title], UserInterface::$titlesStack);
	}

	public function testSetTitleAndDisplayNow(): void
	{
		$this->expectOutputString("\e[0m\e[32m\e[49m| Bye World |\t\e[0m\e[39m\e[49m\n");
		$title1 = "Hello World";
		$title2 = "Bye World";
		UserInterface::setTitle($title1);
		UserInterface::setTitle($title2, true);

		$this->assertEquals([$title1, $title2], UserInterface::$titlesStack);
	}
	
	public function testPopTitle(): void
	{
		$title1 = "Hello World";
		$title2 = "Bye World";
		UserInterface::setTitle($title1);
		UserInterface::setTitle($title2);
		UserInterface::popTitle($title2);

		$this->assertEquals([$title1], UserInterface::$titlesStack);
	}

	public function testPopTitleEmptyStack(): void
	{
		UserInterface::popTitle();
		$this->assertEmpty(UserInterface::$titlesStack);
	}

	public function testDisplayTitle(): void
	{
		$title1 = "Hello World1";
		$title2 = "Hello World2";
		$title3 = "Hello World3";
		$this->expectOutputString("\e[0m\e[32m\e[49m| $title3 |\t\e[0m\e[39m\e[49m");
		UserInterface::setTitle($title1);
		UserInterface::setTitle($title2);
		UserInterface::setTitle($title3);
		UserInterface::displayTitle();
		
		//check the internal pointer is correctly reset
		$this->assertEquals([$title1, $title2, $title3], UserInterface::$titlesStack);
	}

	public function testDisplayTitleEmptyTitleStack(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("No title set");
		UserInterface::displayTitle();
	}

	public function testCleanCamelCase(): void
	{
		$str = "iAmCamelCase";
		$result = UserInterface::cleanCamelCase($str);

		$this->assertEquals($result, "i am camel case");
	}

	public function testCleanCamelCaseWithPascalCase(): void
	{
		$str = "YouAreCamelCase";
		$result = UserInterface::cleanCamelCase($str);

		$this->assertEquals($result, "you are camel case");
	}

	public function testToCamelCase(): void
	{
		$str = "i am camel case";
		$result = UserInterface::toCamelCase($str);

		$this->assertEquals($result, "iAmCamelCase");
	}

	public function testToCamelCaseWithPascalCaseTrick(): void
	{
		$str = "You are camel case";
		$result = UserInterface::toCamelCase($str);

		$this->assertEquals($result, "youAreCamelCase");
	}
}
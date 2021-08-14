<?php

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\throwException;

require_once 'src/Field.php';

final class FieldTest extends TestCase
{
	public function testYesNoErrorHandler(): void
	{
		Field::YesNoErrorHandler("Y");
		$this->expectNotToPerformAssertions();
	}

	public function testYesNoErrorHandlerOnError(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Please type 'Y', 'N' or leave empty");
		Field::YesNoErrorHandler("Hello");
	}

	public function testYesNoDataCleanerForYes(): void
	{
		$this->assertTrue(Field::YesNoDataCleaner('y'));
		$this->assertTrue(Field::YesNoDataCleaner('Y'));
		$this->assertTrue(Field::YesNoDataCleaner('yes'));
	}
	
	public function testYesNoDataCleanerForNo(): void
	{
		$this->assertFalse(Field::YesNoDataCleaner('n'));
		$this->assertFalse(Field::YesNoDataCleaner('N'));
		$this->assertFalse(Field::YesNoDataCleaner('no'));
	}
	
	public function testEmptyDataCleaner(): void
	{
		$input = "Marvinette";
		$this->assertNull(Field::EmptyDataCleaner(''));
		$this->assertEquals(Field::EmptyDataCleaner($input), $input);
	}
	
	public function testSetterCallsAndReturn(): Field
	{
		$value = "  Marvinette  ";
		$this->expectOutputString("Hello World");
		$errorHandler = function ($_) { echo "Hello ";};
		$dataCleaner = function ($data) { echo "World"; return $data;};
		$field = new Field($errorHandler, $dataCleaner, "Hllo");
		
		$field->set($value);
		$this->assertEquals($field->get(), $value);
		$this->assertEquals($field->getPromptHelp(), "Hllo");
		return $field;
	}
	
	public function testSetterWithCleaning(): Field
	{
		$errorHandler = function ($_) {};
		$dataCleaner = function ($data) { return trim($data);};
		$field = new Field($errorHandler, $dataCleaner);

		$field->set("  Marvinette  ");
		$this->assertEquals($field->get(), "Marvinette");
		return $field;
	}

	public function testSetterWithError(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("I Threw");
		$errorHandler = function ($_) { throw new Exception("I Threw");};
		$field = new Field($errorHandler);

		$field->set("  Marvinette  ");
	}

	/**
	 * @depends FieldTest::testSetterWithCleaning
	 */
	public function testToString(Field $field): void
	{
		$this->setOutputCallback(function() {});
		$field->set("Hello World");
		$this->assertTrue($field == "Hello World");
		$field->set(123);
		$this->assertTrue($field == "123");
		$field->set(false);
		$this->assertTrue($field == "");
		$field->set(true);
		$this->assertTrue($field == "1");
	}
}
<?php

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\throwException;

require_once 'src/Project.php';

final class ProjectTest extends TestCase
{

	public function testSetName(): void
	{
		$project = new Project();
		$project->name->set("MY NAME");
		$this->assertEquals($project->name->get(), "MY NAME");
		$this->assertEquals($project->name, "MY NAME");
	}

	public function testSetNameEmptyValue(): void
	{
		$thrown = false;
		$project = new Project();
		$project->name->set("MY NAME");
		try {
			$project->name->set("");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($project->name->get(), "MY NAME");
	}

	public function testSetBinaryName(): void
	{
		$project = new Project();
		$project->binaryName->set("MY BINARY NAME");
		$this->assertEquals($project->binaryName->get(), "MY BINARY NAME");
		$this->assertEquals($project->binaryName, "MY BINARY NAME");
	}

	public function testSetBinaryNameEmptyValue(): void
	{
		$thrown = false;
		$project = new Project();
		$project->binaryName->set("MY BINARY NAME");
		try {
			$project->binaryName->set("");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($project->binaryName->get(), "MY BINARY NAME");
	}

	public function testSetBinaryNameWithSlash(): void
	{
		$thrown = false;
		$project = new Project();
		$project->binaryName->set("MY BINARY NAME");
		try {
			$project->binaryName->set("a/n");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($project->binaryName->get(), "MY BINARY NAME");
	}

	public function testSetBinaryPathDefaultValue(): void
	{
		$project = new Project();
		$this->assertEquals($project->binaryPath, ".");
	}

	public function testSetBinaryPath(): void
	{
		$project = new Project();
		$project->binaryPath->set("MY BINARY PATH");
		$this->assertEquals($project->binaryPath->get(), "MY BINARY PATH");
		$this->assertEquals($project->binaryPath, "MY BINARY PATH");
	}

	public function testSetBinaryPathEmptyValue(): void
	{
		$project = new Project();
		$project->binaryPath->set("");
		$this->assertEquals($project->binaryPath->get(), ".");
		$this->assertEquals($project->binaryPath, ".");
	}

	public function testSetBinaryPathTrailingSlash(): void
	{
		$project = new Project();
		$project->binaryPath->set("a///");
		$this->assertEquals($project->binaryPath->get(), "a");
		$this->assertEquals($project->binaryPath, "a");
	}

	public function testSetInterpreter(): void
	{
		$project = new Project();
		$project->interpreter->set("python3");
		$this->assertEquals($project->interpreter->get(), "python3");
		$this->assertEquals($project->interpreter, "python3");
	}

	public function testSetInterpreterEmptyValue(): void
	{
		$project = new Project();
		$project->binaryPath->set("");
		$this->assertNull($project->interpreter->get());
	}

	public function testSetTestsFolder(): void
	{
		$project = new Project();
		$project->testsFolder->set("testers/");
		$this->assertEquals($project->testsFolder->get(), "testers");
		$this->assertEquals($project->testsFolder, "testers");
	}

	public function testSetTestsFolderDefaultValue(): void
	{
		$project = new Project();
		$this->assertEquals($project->testsFolder, "tests");
	}

	public function testSetTestsFolderEmptyValue(): void
	{
		$project = new Project();
		$project->testsFolder->set("");
		$this->assertEquals($project->testsFolder->get(), "tests");
		$this->assertEquals($project->testsFolder, "tests");
	}

	public function testReadyToExport(): void
	{
		$project = new Project();
		
		$this->assertFalse($project->readyToExport());
		
		$project->name->set('name');
		$this->assertFalse($project->readyToExport());

		$project->binaryName->set('binary');
		
		$this->assertTrue($project->readyToExport());
	}

	public function testBuildBinaryAccessPath(): void
	{
		$project = new Project();

		$project->binaryPath->set("tests/");
		$project->binaryName->set("binary");
		$this->assertEquals($project->buildBinaryAccessPath(), "tests/binary");
	}

	public function testInterpreterExists() :void
	{
		$project = new Project();
		$project->interpreter->set('python3');
		$this->assertTrue($project->interpreterExists());
	}

	public function testInterpreterDoesNotExists() :void
	{
		$project = new Project();
		$project->interpreter->set('TROLOLOL');
		$this->assertFalse($project->interpreterExists());
	}

	public function testInterpreterExistsNoValue() :void
	{
		$project = new Project();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No Interpreter set');
		$this->assertFalse($project->interpreterExists());
	}
}
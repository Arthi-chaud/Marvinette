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

	public function testInterpreterExists(): void
	{
		$project = new Project();
		$project->interpreter->set('python3');
		$this->assertTrue($project->interpreterExists());
	}

	public function testInterpreterDoesNotExists(): void
	{
		$project = new Project();
		$project->interpreter->set('TROLOLOL');
		$this->assertFalse($project->interpreterExists());
	}

	public function testInterpreterExistsNoValue(): void
	{
		$project = new Project();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No Interpreter set');
		$this->assertFalse($project->interpreterExists());
	}

	public function testIsReadyToBeTested(): void
	{
		$project = new Project();

		$project->name->set('name');
		$project->binaryPath->set("./");
		$project->binaryName->set("README.md");
		$this->assertTrue($project->isReadyToBeTested());
	}

	public function testIsReadyToBeTestedValidInterpreter(): void
	{
		$project = new Project();

		$project->name->set('name');
		$project->binaryPath->set("./");
		$project->binaryName->set("README.md");
		$project->interpreter->set('bash');
		$this->assertTrue($project->isReadyToBeTested());
	}

	public function testIsReadyToBeTestedInvalidInterpreter(): void
	{
		$project = new Project();

		$project->name->set('name');
		$project->binaryPath->set("./");
		$project->binaryName->set("README.md");
		$project->interpreter->set('trololol');
		$this->assertFalse($project->isReadyToBeTested());
	}

	public function testIsReadyToBeTestedMissingMandatoryField(): void
	{
		$project = new Project();

		$project->binaryPath->set("./");
		$project->binaryName->set("README.md");
		$this->assertFalse($project->isReadyToBeTested());
	}

	public function testIsReadyToBeTestedBinaryNotFound(): void
	{
		$project = new Project();

		$project->name->set('name');
		$project->binaryPath->set("./");
		$project->binaryName->set("UNKNOWN");
		$this->assertFalse($project->isReadyToBeTested());
	}
	
	public function testExport(): void
	{
		$project = new Project();
		$project->name->set('name');
		$project->binaryPath->set("./");
		$project->binaryName->set("README.md");
		$project->testsFolder->set('testers/');
		$project->export('/tmp/out.json');
		$this->assertTrue(file_exists('/tmp/out.json'));
		$object = json_decode(file_get_contents('/tmp/out.json'), true);

		$this->assertEquals($object['name'], 'name');
		$this->assertEquals($object['binary path'], '.');
		$this->assertEquals($object['binary name'], 'README.md');
		$this->assertNull($object['interpreter']);
		$this->assertEquals($object['tests folder'], 'testers');
	}

	public function testExportWhenNotReady(): void
	{
		$thrown = false;
		$project = new Project();
		try {
			$project->export("/tmp/out2.json");
		} catch (Exception $e) {
			$thrown = true;
			$this->assertEquals($e->getMessage(), "Project is not ready to be exported, missing mandatory field");
		}
		$this->assertTrue($thrown);
		$this->assertFalse(file_exists('/tmp/out2.json'));
	}

	public function testImport(): void
	{
		$project = new Project();

		$project->import('/tmp/out.json');
		$this->assertEquals($project->name->get(), 'name');
		$this->assertEquals($project->binaryPath->get(), '.');
		$this->assertEquals($project->binaryName->get(), 'README.md');
		$this->assertNull($project->interpreter->get());
		$this->assertEquals($project->testsFolder->get(), 'testers');
		//unlink('/tmp/out.json');
	}

	public function testImportNoFile(): void
	{
		$project = new Project();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("/tmp/trololol does not exists.");
		$project->import('/tmp/trololol');
	}

	public function testImportInvalidFile(): void
	{
		$project = new Project();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("File README.md: Invalid JSON File.");
		$project->import('README.md');
	}
}
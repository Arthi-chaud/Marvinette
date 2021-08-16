<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;

require_once 'src/Test.php';
require_once 'src/Project.php';

final class TestTest extends TestCase
{

	public function setUp(): void
	{
		$project = new Project();

		$project->name->set('Name');
		$project->binaryName->set('README.md');
		$project->export('/tmp/out.json');
	}

	public function tearDown(): void
	{
		unlink('/tmp/out.json');
	}

	public function testSetName(): void
	{
		$Test = new Test();
		$Test->name->set("MY NAME");
		$this->assertEquals($Test->name->get(), "MY NAME");
		$this->assertEquals($Test->name, "MY NAME");
	}

	public function testSetNameEmptyValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->name->set("MY NAME");
		try {
			$Test->name->set("");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->name->get(), "MY NAME");
	}

	public function testSetNameWithSlash(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->name->set("MY NAME");
		try {
			$Test->name->set("a/b");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->name->get(), "MY NAME");
	}

	public function testSetCommandLineArguments(): void
	{
		$Test = new Test();
		$Test->commandLineArguments->set("--help");
		$this->assertEquals($Test->commandLineArguments->get(), "--help");
		$this->assertEquals($Test->commandLineArguments, "--help");
	}

	public function testSetCommandLineArgumentsPromptHelp(): void
	{
		$Test = new Test();
		$this->assertEquals($Test->commandLineArguments->getPromptHelp(), "The arguments to pass to the program");
	}

	public function testSetExpectedReturnCode(): void
	{
		$Test = new Test();
		$Test->expectedReturnCode->set("0");
		$this->assertEquals($Test->expectedReturnCode->get(), 0);
		$Test->expectedReturnCode->set("10");
		$this->assertEquals($Test->expectedReturnCode->get(), 10);
	}

	public function testSetExpectedReturnCodeInvalidString(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("aaa");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetExpectedReturnCodeEmptyString(): void
	{
		$Test = new Test();
		$Test->expectedReturnCode->set("");
		$this->assertEquals($Test->expectedReturnCode->get(), null);
	}

	public function testSetExpectedReturnCodeTooHighValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("1000");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetExpectedReturnCodeTooLowValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("-1");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetStdoutFilter(): void
	{
		$Test = new Test();
		$Test->stdoutFilter->set("grep 'ok'");
		$this->assertEquals($Test->stdoutFilter->get(), "grep 'ok'");
		$this->assertEquals($Test->stdoutFilter, "grep 'ok'");
	}

	public function testSetStdoutFilterEmptyValue(): void
	{
		$Test = new Test();
		$Test->stdoutFilter->set("");
		$this->assertEquals($Test->stdoutFilter->get(), null);
	}

	public function testSetStderrFilter(): void
	{
		$Test = new Test();
		$Test->stderrFilter->set("grep 'ok'");
		$this->assertEquals($Test->stderrFilter->get(), "grep 'ok'");
		$this->assertEquals($Test->stderrFilter, "grep 'ok'");
	}

	public function testSetStderrFilterEmptyValue(): void
	{
		$Test = new Test();
		$Test->stderrFilter->set("");
		$this->assertEquals($Test->stderrFilter->get(), null);
	}

	public function testSetStdinput(): void
	{
		$Test = new Test();
		$Test->stdinput->set("Y");
		$this->assertEquals($Test->stdinput->get(), true);
		$Test->stdinput->set("N");
		$this->assertEquals($Test->stdinput->get(), false);
	}

	public function testSetStdinputDefaultValue(): void
	{
		$Test = new Test();
		$Test->stdinput->set("");
		$this->assertEquals($Test->stdinput->get(), false);
	}

	public function testSetStdinputInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->stdinput->set("Y");
		try {
			$Test->stdinput->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->stdinput->get(), true);
	}

	public function testSetExpectedStdout(): void
	{
		$Test = new Test();
		$Test->expectedStdout->set("Y");
		$this->assertEquals($Test->expectedStdout->get(), true);
		$Test->expectedStdout->set("N");
		$this->assertEquals($Test->expectedStdout->get(), false);
	}

	public function testSetExpectedStdoutDefaultValue(): void
	{
		$Test = new Test();
		$Test->expectedStdout->set("");
		$this->assertEquals($Test->expectedStdout->get(), false);
	}

	public function testSetExpectedStdoutInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedStdout->set("Y");
		try {
			$Test->expectedStdout->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedStdout->get(), true);
	}


	public function testSetExpectedStderr(): void
	{
		$Test = new Test();
		$Test->expectedStderr->set("Y");
		$this->assertEquals($Test->expectedStderr->get(), true);
		$Test->expectedStderr->set("N");
		$this->assertEquals($Test->expectedStderr->get(), false);
	}

	public function testSetExpectedStderrDefaultValue(): void
	{
		$Test = new Test();
		$Test->expectedStderr->set("");
		$this->assertEquals($Test->expectedStderr->get(), false);
	}

	public function testSetExpectedStderrInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedStderr->set("Y");
		try {
			$Test->expectedStderr->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedStderr->get(), true);
	}

	public function testSetSetup(): void
	{
		$Test = new Test();
		$Test->setup->set("dnf install");
		$this->assertEquals($Test->setup->get(), "dnf install");
		$this->assertEquals($Test->setup, "dnf install");
	}

	public function testSetSetupEmptyValue(): void
	{
		$Test = new Test();
		$Test->setup->set("");
		$this->assertEquals($Test->setup->get(), null);
	}

	public function testSetTearDown(): void
	{
		$Test = new Test();
		$Test->teardown->set("rm file");
		$this->assertEquals($Test->teardown->get(), "rm file");
		$this->assertEquals($Test->teardown, "rm file");
	}

	public function testSetTearDownEmptyValue(): void
	{
		$Test = new Test();
		$Test->teardown->set("");
		$this->assertEquals($Test->teardown->get(), null);
	}

	private function getDummyTest(): Test
	{
		$Test = new Test();
		$Test->name->set('101');
		$Test->commandLineArguments->set('--argc 1 --argv 2');
		$Test->expectedReturnCode->set('10');
		$Test->stdoutFilter->set('grep \'hello\'');
		$Test->stderrFilter->set('grep \'world\'');
		$Test->expectedStdout->set('Y');
		$Test->expectedStderr->set('Y');
		$Test->stdinput->set('Y');
		$Test->setup->set('set me up');
		$Test->teardown->set('tear me down');
		return $Test;
	}

	public function testExport(): void
	{
		$Test = $this->getDummyTest();
		$Test->export('tests/');

		$this->assertTrue(is_dir('tests/101'));
		$this->assertTrue(file_exists('tests/101/commandLineArguments'));
		$this->assertTrue(file_exists('tests/101/expectedReturnCode'));
		$this->assertTrue(file_exists('tests/101/stdoutFilter'));
		$this->assertTrue(file_exists('tests/101/stderrFilter'));
		$this->assertTrue(file_exists('tests/101/expectedStdout'));
		$this->assertTrue(file_exists('tests/101/expectedStderr'));
		$this->assertTrue(file_exists('tests/101/setup'));
		$this->assertTrue(file_exists('tests/101/stdinput'));
		$this->assertTrue(file_exists('tests/101/teardown'));

		$this->assertEquals(file_get_contents('tests/101/commandLineArguments'), '--argc 1 --argv 2');
		$this->assertEquals(file_get_contents('tests/101/expectedReturnCode'), '10');
		$this->assertEquals(file_get_contents('tests/101/stdoutFilter'), "grep 'hello'");
		$this->assertEquals(file_get_contents('tests/101/stderrFilter'),  "grep 'world'");
		$this->assertEquals(file_get_contents('tests/101/expectedStdout'), '');
		$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertEquals(file_get_contents('tests/101/setup'), 'set me up');
		$this->assertEquals(file_get_contents('tests/101/teardown'), 'tear me down');
		$this->assertEquals(file_get_contents('tests/101/stdinput'), '');
		FileManager::deleteFolder('tests/101');
	}

	public function testExportReturnCodeZero(): void
	{
		$Test = $this->getDummyTest();
		$Test->expectedReturnCode->set('0');
		$Test->export('tests/');

		$this->assertTrue(file_exists('tests/101/expectedReturnCode'));
		$this->assertEquals(file_get_contents('tests/101/expectedReturnCode'), '0');
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoCommandLineArguments(): void
	{
		$Test = $this->getDummyTest();
		$Test->commandLineArguments->set('');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/commandLineArguments'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoExpectedReturnCode(): void
	{
		$Test = $this->getDummyTest();
		$Test->expectedReturnCode->set('');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/expectedReturnCode'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoStdinput(): void
	{
		$Test = $this->getDummyTest();
		$Test->stdinput->set('N');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/stdinput'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoExpectedStderr(): void
	{
		$Test = $this->getDummyTest();
		$Test->expectedStderr->set('N');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/expectedStderr'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoExpectedStdout(): void
	{
		$Test = $this->getDummyTest();
		$Test->expectedStdout->set('N');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/expectedStdout'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoSetup(): void
	{
		$Test = $this->getDummyTest();
		$Test->setup->set('');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/setup'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoTearDown(): void
	{
		$Test = $this->getDummyTest();
		$Test->teardown->set('');
		$Test->export('tests/');

		$this->assertFalse(file_exists('tests/101/teardown'));
		FileManager::deleteFolder('tests/101');
	}

	public function testImport(): Test
	{
		$TestFrom = $this->getDummyTest();
		$TestFrom->stdinput->set('N');
		$TestFrom->export('tests/');

		$TestTo = new Test('tests/101');
		$this->assertEquals($TestTo->name->get(), '101');
		$this->assertEquals($TestTo->commandLineArguments->get(), '--argc 1 --argv 2');
		$this->assertEquals($TestTo->expectedReturnCode->get(), '10');
		$this->assertEquals($TestTo->stdoutFilter->get(), 'grep \'hello\'');
		$this->assertEquals($TestTo->stderrFilter->get(), 'grep \'world\'');
		$this->assertEquals($TestTo->expectedStdout->get(), true);
		$this->assertEquals($TestTo->expectedStderr->get(), true);
		$this->assertEquals($TestTo->stdinput->get(), false);
		$this->assertEquals($TestTo->setup->get(), 'set me up');
		$this->assertEquals($TestTo->teardown->get(), 'tear me down');
		FileManager::deleteFolder('tests/101');
		return $TestTo;
	}

	/**
	 * @depends TestTest::testImport
	 */
	public function testReExportRemovingExistingFile($testTo): Test
	{
		$testTo->expectedReturnCode->set('');
		$testTo->export('tests/');
		$this->assertEquals(file_get_contents('tests/101/commandLineArguments'), '--argc 1 --argv 2');
		$this->assertFalse(file_exists('tests/101/expectedReturnCode'));
		$this->assertEquals(file_get_contents('tests/101/stdoutFilter'), "grep 'hello'");
		$this->assertEquals(file_get_contents('tests/101/stderrFilter'),  "grep 'world'");
		$this->assertEquals(file_get_contents('tests/101/expectedStdout'), '');
		$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertFalse(file_exists('tests/101/stdinput'));
		$this->assertEquals(file_get_contents('tests/101/teardown'), 'tear me down');
		$this->assertEquals(file_get_contents('tests/101/setup'), 'set me up');
		return $testTo;
	}

	/**
	 * @depends TestTest::testReExportRemovingExistingFile
	 */
	public function testReExportAddingFile($testTo): void
	{
		$testTo->expectedReturnCode->set('99');
		$testTo->export('tests/');
		$this->assertEquals(file_get_contents('tests/101/commandLineArguments'), '--argc 1 --argv 2');
		$this->assertEquals(file_get_contents('tests/101/expectedReturnCode'), '99');
		$this->assertEquals(file_get_contents('tests/101/stdoutFilter'), "grep 'hello'");
		$this->assertEquals(file_get_contents('tests/101/stderrFilter'),  "grep 'world'");
		$this->assertEquals(file_get_contents('tests/101/expectedStdout'), '');
		$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertFalse(file_exists('tests/101/stdinput'));
		$this->assertEquals(file_get_contents('tests/101/teardown'), 'tear me down');
		$this->assertEquals(file_get_contents('tests/101/setup'), 'set me up');
		FileManager::deleteFolder('tests/101');
	}
}
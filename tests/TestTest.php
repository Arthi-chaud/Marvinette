<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;

require_once 'src/Test.php';
require_once 'src/Project.php';
require_once 'tests/MarvinetteTestCase.php';

final class TestTest extends MarvinetteTestCase
{

	public function setUp(): void
	{
		$project = new Project();

		$project->name->set('Name');
		$project->binaryName->set('README.md');
		$project->export('out.json');
	}

	public function tearDown(): void
	{
		unlink('out.json');
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
			$Test->name->set('a' . DIRECTORY_SEPARATOR . 'b');
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
		$this->assertTrue(file_exists('tests/101/config.json'));
		$jsonContent = json_decode(file_get_contents("tests/101/config.json"), true);

		$this->assertTrue(array_key_exists('commandLineArguments', $jsonContent));
		$this->assertTrue(array_key_exists('expectedReturnCode', $jsonContent));
		$this->assertTrue(array_key_exists('stdoutFilter', $jsonContent));
		$this->assertTrue(array_key_exists('stderrFilter', $jsonContent));
		$this->assertFalse(array_key_exists('expectedStdout', $jsonContent));
		$this->assertFalse(array_key_exists('expectedStderr', $jsonContent));
		$this->assertTrue(array_key_exists('setup', $jsonContent));
		$this->assertFalse(array_key_exists('stdinput', $jsonContent));
		$this->assertTrue(array_key_exists('teardown', $jsonContent));

		$this->assertEquals($jsonContent['commandLineArguments'], '--argc 1 --argv 2');
		$this->assertEquals($jsonContent['expectedReturnCode'], '10');
		$this->assertEquals($jsonContent['stdoutFilter'], "grep 'hello'");
		$this->assertEquals($jsonContent['stderrFilter'],  "grep 'world'");
		$this->assertEquals($jsonContent['setup'], 'set me up');
		$this->assertEquals($jsonContent['teardown'], 'tear me down');

		$this->assertEquals(file_get_contents('tests/101/expectedStdout'), '');
		$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertEquals(file_get_contents('tests/101/stdinput'), '');
		FileManager::deleteFolder('tests/101');
	}

	public function testExportReturnCodeZero(): void
	{
		$Test = $this->getDummyTest();
		$Test->expectedReturnCode->set('0');
		$Test->export('tests/');

		$jsonContent = json_decode(file_get_contents("tests/101/config.json"), true);
		$this->assertTrue(array_key_exists('expectedReturnCode', $jsonContent));
		$this->assertEquals($jsonContent['expectedReturnCode'], 0);
		$this->assertFalse(is_null($jsonContent['expectedReturnCode']));
		FileManager::deleteFolder('tests/101');
	}

	public function testExportNoCommandLineArguments(): void
	{
		$Test = $this->getDummyTest();
		$Test->commandLineArguments->set('');
		$Test->export('tests/');

		$this->assertTrue(is_dir('tests/101'));
		$this->assertTrue(file_exists('tests/101/config.json'));
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

	public function testImportInvalidDir(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid test path');

		new Test('ta mere en slip');
	}

	/**
	 * @depends TestTest::testImport
	 */
	public function testReExportRemovingExistingFile($testTo): Test
	{
		$testTo->expectedReturnCode->set('');
		$testTo->export('tests/');
		$jsonContent = json_decode(file_get_contents("tests/101/config.json"), true);
		$this->assertFalse(file_exists('tests/101/stdinput'));
		$this->assertTrue(file_exists('tests/101/expectedStdout'));
		$this->assertTrue(file_exists('tests/101/expectedStderr'));
		$this->assertNull($jsonContent['expectedReturnCode']);
		$this->assertEquals($jsonContent['commandLineArguments'], '--argc 1 --argv 2');
		$this->assertEquals($jsonContent['stdoutFilter'], "grep 'hello'");
		$this->assertEquals($jsonContent['stderrFilter'],  "grep 'world'");
		$this->assertEquals($jsonContent['teardown'], 'tear me down');
		$this->assertEquals($jsonContent['setup'], 'set me up');
		return $testTo;
	}

	/**
	 * @depends TestTest::testReExportRemovingExistingFile
	 */
	public function testReExportAddingFile($testTo): void
	{
		$testTo->expectedReturnCode->set('99');
		$testTo->export('tests/');
		$jsonContent = json_decode(file_get_contents("tests/101/config.json"), true);
		$this->assertEquals($jsonContent['commandLineArguments'], '--argc 1 --argv 2');
		$this->assertEquals($jsonContent['expectedReturnCode'], 99);
		$this->assertEquals($jsonContent['stdoutFilter'], "grep 'hello'");
		$this->assertEquals($jsonContent['stderrFilter'],  "grep 'world'");
		$this->assertEquals($jsonContent['teardown'], 'tear me down');
		$this->assertEquals($jsonContent['setup'], 'set me up');
		$this->assertTrue(file_exists('tests/101/expectedStdout'));
		$this->assertTrue(file_exists('tests/101/expectedStderr'));
		$this->assertFalse(file_exists('tests/101/stdinput'));
		FileManager::deleteFolder('tests/101');
	}

	public function testExecuteSystemCommand(): void
	{
		$test = new Test();

		$this->expectOutputString("Hello World" . PHP_EOL);
		$this->callMethod($test, 'executeSystemCommand', ['echo Hello World', null, 0]);
	}

	public function testExecuteSystemCommandThrowingError(): void
	{
		$test = new Test();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Return code: 1");
		$this->expectOutputString("cat: Hello.jpg: No such file or directory\n");
	
		$this->callMethod($test, 'executeSystemCommand', ['cat Hello.jpg 2>&1']);
	}

	public function testExecuteSystemCommandThrowingErrorCustomMessage(): void
	{
		$test = new Test();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Oh shoot... Return code: 1");
		$this->expectOutputString("cat: Hello.jpg: No such file or directory\n");
	
		$this->callMethod($test, 'executeSystemCommand', ['cat Hello.jpg 2>&1', "Oh shoot..."]);
	}

	public function testExecuteSystemCommandExpectingNotNullReturnCode(): void
	{
		$test = new Test();
		$this->expectOutputString("cat: Hello.jpg: No such file or directory\n");
	
		$this->callMethod($test, 'executeSystemCommand', ['cat Hello.jpg 2>&1', null, 1]);
	}

	public function testBuildCommand(): void
	{
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');
		
		$test = new Test();
		$test->interpreterArguments->set("-E");
		$test->name->set("First Example");
		$test->commandLineArguments->set("100 15");
		$test->expectedReturnCode->set("0");
		$test->stdoutFilter->set("head -n 2");
		$test->expectedStdout->set("Y");

		$command = $this->callMethod($test, 'buildCommand', [$project, 'tmp/First Example']);
		$expected = "-E tests" . DIRECTORY_SEPARATOR . "MYFAKEPROJECT.py 100 15";
		$expected .= ' > ' . TmpFileFolder . DIRECTORY_SEPARATOR . 'MarvinetteStdout';
		$expected .= ' 2> ' . TmpFileFolder . DIRECTORY_SEPARATOR . 'MarvinetteStderr';
		$this->assertStringContainsString($expected, $command);
	}


	public function testBuildCommandWithEmptyEnv(): void
	{
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');
		
		$test = new Test();
		$test->interpreterArguments->set("-E");
		$test->name->set("First Example");
		$test->commandLineArguments->set("100 15");
		$test->expectedReturnCode->set("0");
		$test->stdoutFilter->set("head -n 2");
		$test->emptyEnv->set(true);
		$test->expectedStdout->set("Y");

		$command = $this->callMethod($test, 'buildCommand', [$project, 'tmp/First Example']);
		$expected = "-E tests" . DIRECTORY_SEPARATOR . "MYFAKEPROJECT.py 100 15";
		$expected .= ' > ' . TmpFileFolder . DIRECTORY_SEPARATOR . 'MarvinetteStdout';
		$expected .= ' 2> ' . TmpFileFolder . DIRECTORY_SEPARATOR . 'MarvinetteStderr';
		$this->assertStringContainsString($expected, $command);
		$this->assertStringContainsString("env -i", $command);
	}

	public function testExecute(): void
	{
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');

		$test = new Test();
		$test->name->set("First Example");
		$test->commandLineArguments->set("100 15");
		$test->expectedReturnCode->set("0");
		$test->stdoutFilter->set("head -n 2");
		$test->expectedStdout->set("Y");
		$test->setup->set("touch Setup");
		$test->teardown->set("touch Teardwon");
		$test->export($project->testsFolder->get());
		file_put_contents('tmp/First Example/expectedStdout', "0 0.00000\n1 0.00000\n");
		$test->import('tmp/First Example');
		$test->execute($project);
		$this->assertTrue(true);
		$this->assertTrue(file_exists('Setup'));
		$this->assertTrue(file_exists('Teardwon'));
		unlink('Setup');
		unlink('Teardwon');
		FileManager::deleteFolder('tmp/First Example');
	}

	public function testExecuteAnotherTestIgnoringReturnCode(): void
	{
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');

		$test = new Test();
		$test->name->set("Second Example");
		$test->commandLineArguments->set("100 15");
		$test->stdoutFilter->set("tail -n 2");
		$test->expectedStdout->set("Y");
		$test->setup->set("touch Setup");
		$test->teardown->set("touch Teardwon");
		$test->export($project->testsFolder->get());
		file_put_contents('tmp/Second Example/expectedStdout', "199 0.00000\n200 0.00000\n");
		$test->import('tmp/Second Example');
		$test->execute($project);
		$this->assertTrue(true);
		$this->assertTrue(file_exists('Setup'));
		$this->assertTrue(file_exists('Teardwon'));
		unlink('Setup');
		unlink('Teardwon');
		FileManager::deleteFolder('tmp/Second Example');
	}

	public function testExecuteBadOutput(): void
	{
		UserInterface::setTitle("Test Bad Output");
		$catched = false;
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');

		$test = new Test();
		$test->name->set("Third Example");
		$test->commandLineArguments->set("100 15");
		$test->expectedReturnCode->set("0");
		$test->stdoutFilter->set("tail -n 2");
		$test->expectedStdout->set("Y");
		$test->export($project->testsFolder->get());
		file_put_contents('tmp/Third Example/expectedStdout', "199 0.00000\n200 0.00000\n\n");
		$test->import('tmp/Third Example');
		try {
			$test->execute($project);
		} catch (Exception $e) {
			$catched = true;
			$this->assertEquals($e->getMessage(), "Expected Output differs. Return code: 1");
		}
		$this->assertTrue($catched);
		FileManager::deleteFolder('tmp/Third Example');
	}

	public function testExecuteWrongReturnCode(): void
	{
		$catched = false;
		$project = new Project();
		$project->name->set('101');
		$project->binaryName->set('MYFAKEPROJECT.py');
		$project->binaryPath->set('tests/');
		$project->interpreter->set('python');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');

		$test = new Test();
		$test->name->set("Fourth Example");
		$test->commandLineArguments->set("100 15");
		$test->expectedReturnCode->set("1");
		$test->stdoutFilter->set("tail -n 2");
		$test->expectedStdout->set("Y");
		$test->export($project->testsFolder->get());
		file_put_contents('tmp/Fourth Example/expectedStdout', "199 0.00000\n200 0.00000\n");
		$test->import('tmp/Fourth Example');
		try {
			$test->execute($project);
		} catch (Exception $e) {
			$catched = true;
			$this->assertEquals($e->getMessage(), "Returned 0 instead of 1");
		}
		$this->assertTrue($catched);
		FileManager::deleteFolder('tmp/Fourth Example');
	}

	public function testExecuteWithStdinput(): void
	{
		$project = new Project();
		$project->name->set('101');
		$project->interpreter->set('python');
		$project->binaryName->set('python');
		$project->binaryPath->set(dirname($project->getInterpreterFullPath()));
		$project->interpreter->set('');
		$project->testsFolder->set('tmp/');
		$project->export('tmp/Marvinette.json');

		$test = new Test();
		$test->name->set("Fifth Example");
		$test->expectedReturnCode->set("0");
		$test->stdinput->set("Y");
		$test->expectedStdout->set("Y");
		$test->export($project->testsFolder->get());
		file_put_contents('tmp/Fifth Example/expectedStdout', "Hello\nwell ...\nbye\n");
		file_put_contents('tmp/Fifth Example/stdinput', "print('Hello')\nprint('well ...')\nprint('bye')\n");
		$test->import('tmp/Fifth Example');
		$test->execute($project);
		$this->assertTrue(true);
		FileManager::deleteFolder('tmp/Fifth Example');
	}

	public function testCompareOutputInvalidStream(): void
	{
		$test = new Test();

		$this->expectException(Exception::class);
		$this->expectExceptionMessage("compareOutput: 'badName' is an invalid stream name");
		$this->callMethod($test, 'compareOutput', ['badName', 'tests/bad']);
	}

	public function testFilterOutputInvalidStream(): void
	{
		$test = new Test();

		$this->expectException(Exception::class);
		$this->expectExceptionMessage("filterOutput: 'badName' is an invalid stream name");
		$this->callMethod($test, 'filterOutput', ['badName', 'tests/bad']);
	}
}
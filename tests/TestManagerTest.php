<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;

require_once 'src/Test.php';
require_once 'src/TestManager.php';
require_once 'src/Project.php';
require_once 'tests/MarvinetteTestCase.php';

final class TestManagerTest extends MarvinetteTestCase
{
	public function setup(): void
	{
		if (file_exists('Marvinette.json'))
			unlink('Marvinette.json');
		$this->defineStdin([
			'My Name',
			'MYFAKEPROJECT.py',
			'./tests',
			'python',
			'',
			'n'
		]);
		ProjectManager::createProject();
	}

	public function testSelectTest(): void
	{
		mkdir('tests/101');
		touch('tests/101/stdinput');
		mkdir('tests/102');
		touch('tests/102/expectedStdout');
		$this->hideStdout();
		$this->defineStdin([
			'2',
			'3',
			'9',
			'1',

		]);
		$this->assertEquals(TestManager::selectTest(new Project('Marvinette.json')), '102');
		FileManager::deleteFolder('tests/101');
		FileManager::deleteFolder('tests/102');
	}

	public function testSelectTestOneChoice(): void
	{
		mkdir('tests/101');
		touch('tests/101/stdinput');
		$this->hideStdout();
		$this->defineStdin([
			'b',
			'Y'

		]);
		$this->assertEquals(TestManager::selectTest(new Project('Marvinette.json')), '101');
		FileManager::deleteFolder('tests/101');
	}

	public function testSelectTestOneChoiceSayNo(): void
	{
		mkdir('tests/101');
		touch('tests/101/stdinput');
		$this->hideStdout();
		$this->defineStdin([
			'b',
			'n'

		]);
		$this->assertNull(TestManager::selectTest(new Project('Marvinette.json')));
		FileManager::deleteFolder('tests/101');
	}

	public function testSelectTestEmptyFolder(): void
	{
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::selectTest(new Project('Marvinette.json'));
		} catch (InvalidTestFolderException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
	}

	public function testFolderIsATest(): void
	{
		$this->hideStdout();
		mkdir('tests/101');
		touch('tests/101/stdinput');
		mkdir('tests/102');
		touch('tests/102/expectedStdout');
		$this->assertTrue(TestManager::folderIsATest('tests/101'));
		$this->assertTrue(TestManager::folderIsATest('tests/102'));
		FileManager::deleteFolder('tests/101');
		FileManager::deleteFolder('tests/102');
	}

	public function testFolderIsNotATest(): void
	{
		$this->hideStdout();
		mkdir('tests/103');
		$this->assertFalse(TestManager::folderIsATest('tests/103'));
		touch('tests/103/stderrFilter');
		touch('tests/103/lololol');
		$this->assertFalse(TestManager::folderIsATest('tests/103'));
		$this->assertFalse(TestManager::folderIsATest('tests'));
		FileManager::deleteFolder('tests/103');
	}

	public function testDeleteTest(): void
	{
		mkdir('tests/101');
		touch('tests/101/stdinput');
		mkdir('tests/102');
		touch('tests/102/expectedStdout');
		$this->hideStdout();
		$this->defineStdin([
			'0'
		]);
		TestManager::deleteTest();
		$this->assertFalse(is_dir('tests/101'));
		$this->assertTrue(is_dir('tests/102'));
		FileManager::deleteFolder('tests/102');
	}

	public function testModTestNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::modTest();
		} catch (NoConfigFileException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
		rename('M.json', 'Marvinette.json');
	}

	public function testModTestEmptyFolder(): void
	{
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::modTest();
		} catch (InvalidTestFolderException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
	}

	public function testAddTestNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::addTest();
		} catch (NoConfigFileException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
		rename('M.json', 'Marvinette.json');
	}

	public function testDeleteTestNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::deleteTest();
		} catch (NoConfigFileException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
		rename('M.json', 'Marvinette.json');
	}

	public function testExecuteTestNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::executeTest();
		} catch (NoConfigFileException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
		rename('M.json', 'Marvinette.json');
	}
	public function testExecuteTestsNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$throw = false;
		$this->hideStdout();
		try {
			TestManager::executesAllTests();
		} catch (NoConfigFileException $e) {
			$throw = true;
		}
		$this->assertTrue($throw);
		rename('M.json', 'Marvinette.json');
	}

	public function testAddTest()
	{
		$this->hideStdout();
		$this->defineStdin([
			'101',
			'--argc 1 --argv 2',
			'-E',
			'10',
			'grep \'hello\'',
			'grep \'world\'',
			'trololol',
			'Y',
			'n',
			'Y',
			'n',
			'set me up',
			'tear me down',
		]);
		TestManager::addTest();
		$this->assertTrue(is_dir('tests/101'));
		$this->assertTrue(file_exists('tests/101/commandLineArguments'));
		$this->assertTrue(file_exists('tests/101/interpreterArguments'));
		$this->assertTrue(file_exists('tests/101/expectedReturnCode'));
		$this->assertTrue(file_exists('tests/101/stdoutFilter'));
		$this->assertTrue(file_exists('tests/101/stderrFilter'));
		$this->assertTrue(file_exists('tests/101/expectedStdout'));
		$this->assertFalse(file_exists('tests/101/expectedStderr'));
		$this->assertTrue(file_exists('tests/101/setup'));
		$this->assertTrue(file_exists('tests/101/stdinput'));
		$this->assertTrue(file_exists('tests/101/teardown'));

		$this->assertEquals(file_get_contents('tests/101/commandLineArguments'), '--argc 1 --argv 2');
		$this->assertEquals(file_get_contents('tests/101/interpreterArguments'), '-E');
		$this->assertEquals(file_get_contents('tests/101/expectedReturnCode'), '10');
		$this->assertEquals(file_get_contents('tests/101/stdoutFilter'), "grep 'hello'");
		$this->assertEquals(file_get_contents('tests/101/stderrFilter'),  "grep 'world'");
		$this->assertEquals(file_get_contents('tests/101/expectedStdout'), '');
		//$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertEquals(file_get_contents('tests/101/setup'), 'set me up');
		$this->assertEquals(file_get_contents('tests/101/teardown'), 'tear me down');
		$this->assertEquals(file_get_contents('tests/101/stdinput'), '');
		//FileManager::deleteFolder('tests/101');
	}

	public function testModTest()
	{
		$this->hideStdout();
		$this->defineStdin([
			'Y',
			'',
			'',
			'',
			'0',
			'grep \'world1\'',
			'',
			'trololol',
			'Y',
			'n',
			'n',
			'Y',
			'  set me up    ',
			'trololol',
		]);
		TestManager::modTest();
		$this->assertTrue(is_dir('tests/101'));
		$this->assertTrue(file_exists('tests/101/commandLineArguments'));
		$this->assertTrue(file_exists('tests/101/interpreterArguments'));
		$this->assertTrue(file_exists('tests/101/expectedReturnCode'));
		$this->assertTrue(file_exists('tests/101/stdoutFilter'));
		$this->assertTrue(file_exists('tests/101/stderrFilter'));
		$this->assertFalse(file_exists('tests/101/expectedStdout'));
		$this->assertTrue(file_exists('tests/101/expectedStderr'));
		$this->assertTrue(file_exists('tests/101/setup'));
		$this->assertTrue(file_exists('tests/101/stdinput'));
		$this->assertTrue(file_exists('tests/101/teardown'));

		$this->assertEquals(file_get_contents('tests/101/commandLineArguments'), '--argc 1 --argv 2');
		$this->assertEquals(file_get_contents('tests/101/interpreterArguments'), '-E');
		$this->assertEquals(file_get_contents('tests/101/expectedReturnCode'), '0');
		$this->assertEquals(file_get_contents('tests/101/stdoutFilter'), "grep 'world1'");
		$this->assertEquals(file_get_contents('tests/101/stderrFilter'),  "grep 'world'");
		$this->assertEquals(file_get_contents('tests/101/expectedStderr'), '');
		$this->assertEquals(file_get_contents('tests/101/setup'), 'set me up');
		$this->assertEquals(file_get_contents('tests/101/teardown'), 'trololol');
		$this->assertEquals(file_get_contents('tests/101/stdinput'), '');
		FileManager::deleteFolder('tests/101');
	}

	public function testExecuteAllTestsWithoutTests()
	{
		$this->expectException(InvalidTestFolderException::class);
		$this->expectOutputString("| Create Project\t|\tEnter the project's name: | Create Project\t|\tEnter the project's binary name: | Create Project\t|\tEnter the project's binary path (By default: Current directory): | Create Project\t|\tEnter the project's interpreter (By default: none (when it is an ELF file or a script using a shebang)): | Create Project\t|\tEnter the project's tests folder (By default in 'tests' folder): | Create Project\t|\tThe Project's configuration file is created!\n| Create Project\t|\tWould You Like to add a test now [Y/n]: | Executing\t|\tNo tests available\n");
		$this->assertTrue(TestManager::executesAllTests());
	}


	public function testExecuteTest()
	{
		$this->defineStdin([
			'First Test',
			'100 15',
			'',
			'0',
			'head -n 120 | tail -n 10',
			'',
			'',
			'Y',
			'n',
			'n',
			'',
			'',
			'Y'
		]);
		//$this->hideStdout();
		TestManager::addTest();
		file_put_contents('tests/First Test/expectedStdout', "110 0.02130\n111 0.02033\n112 0.01931\n113 0.01827\n114 0.01721\n115 0.01613\n116 0.01506\n117 0.01399\n118 0.01295\n119 0.01192\n");
		$this->assertTrue(TestManager::executeTest());
	}

	public function testExecuteAnotherTest()
	{
		$this->hideStdout();
		$this->defineStdin([
			'Second Test',
			'100 15',
			'',
			'',
			'tail -n 2',
			'',
			'',
			'Y',
			'n',
			'n',
			'',
			'',
			'1',
			''
		]);
		TestManager::addTest();
		file_put_contents('tests/Second Test/expectedStdout', "199 0.00000\n200 0.00000\n");
		$this->assertTrue(TestManager::executeTest());
	}

	public function testExecuteAllTestsWithoutFailure()
	{

		$this->expectOutputString("| Create Project\t|\tEnter the project's name: | Create Project\t|\tEnter the project's binary name: | Create Project\t|\tEnter the project's binary path (By default: Current directory): | Create Project\t|\tEnter the project's interpreter (By default: none (when it is an ELF file or a script using a shebang)): | Create Project\t|\tEnter the project's tests folder (By default in 'tests' folder): | Create Project\t|\tThe Project's configuration file is created!\n| Create Project\t|\tWould You Like to add a test now [Y/n]: | Test 'First Test'\t|\tExecuting Test 'First Test'...\n| Test 'First Test'\t|\tFirst Test: Test passed!\n| Test 'Second Test'\t|\tExecuting Test 'Second Test'...\n| Test 'Second Test'\t|\tSecond Test: Test passed!\n| Executing	|	Test Count: 2 | Success: 2 | Failed: 0\n");
		$this->assertTrue(TestManager::executesAllTests());
	}

	public function testExecuteFailingTest()
	{
		$this->hideStdout();
		$this->defineStdin([
			'Third Test',
			'100 15',
			'',
			'',
			'tail -n 2',
			'',
			'',
			'Y',
			'n',
			'n',
			'',
			'',
			'2',
			''
		]);
		TestManager::addTest();
		file_put_contents('tests/Third Test/expectedStdout', "200 0.00000\n");
		$this->assertFalse(TestManager::executeTest());
	}

	public function testExecuteAllTestsWithFailure()
	{
		$this->expectOutputString("| Create Project\t|\tEnter the project's name: | Create Project\t|\tEnter the project's binary name: | Create Project\t|\tEnter the project's binary path (By default: Current directory): | Create Project\t|\tEnter the project's interpreter (By default: none (when it is an ELF file or a script using a shebang)): | Create Project\t|\tEnter the project's tests folder (By default in 'tests' folder): | Create Project\t|\tThe Project's configuration file is created!\n| Create Project\t|\tWould You Like to add a test now [Y/n]: | Test 'First Test'\t|\tExecuting Test 'First Test'...\n| Test 'First Test'\t|\tFirst Test: Test passed!\n| Test 'Second Test'\t|\tExecuting Test 'Second Test'...\n| Test 'Second Test'\t|\tSecond Test: Test passed!\n| Test 'Third Test'\t|\tExecuting Test 'Third Test'...\n| Test 'Third Test'\t|\tThird Test: Test Failed! Expected Output differs. Return code: 1\n| Test 'Third Test'	|	0a1\n| Test 'Third Test'	|	> 199 0.00000\n| Test 'Third Test'	|	\n| Executing	|	Test Count: 3 | Success: 2 | Failed: 1\n");
		$this->assertFalse(TestManager::executesAllTests());
		FileManager::deleteFolder('tests/First Test');
		FileManager::deleteFolder('tests/Second Test');
		FileManager::deleteFolder('tests/Third Test');
	}

	public function testAddTestForProjectWithoutInterpreter()
	{
		unlink('Marvinette.json');
		$this->defineStdin([
			'My Name',
			'README.me',
			'',
			'',
			'',
			'Y',
			'102',
			'100 15',
			'0',
			'head -n 120 | tail -n 10',
			'',
			'',
			'n',
			'Y',
			'n',
			'',
			'',
			''
		]);
		ProjectManager::createProject();
		$this->assertTrue(is_dir('tests/102'));
		$this->assertTrue(file_exists('tests/102/commandLineArguments'));
		$this->assertFalse(file_exists('tests/102/interpreterArguments'));
		$this->assertTrue(file_exists('tests/102/expectedReturnCode'));
		$this->assertTrue(file_exists('tests/102/stdoutFilter'));
		$this->assertFalse(file_exists('tests/102/stderrFilter'));
		$this->assertTrue(file_exists('tests/102/expectedStdout'));
		$this->assertFalse(file_exists('tests/102/expectedStderr'));
		$this->assertFalse(file_exists('tests/102/setup'));
		$this->assertFalse(file_exists('tests/102/stdinput'));
		$this->assertFalse(file_exists('tests/102/teardown'));

		$this->assertEquals(file_get_contents('tests/102/commandLineArguments'), '100 15');
		$this->assertEquals(file_get_contents('tests/102/expectedReturnCode'), '0');
		$this->assertEquals(file_get_contents('tests/102/stdoutFilter'), "head -n 120 | tail -n 10");
		rename('Marvinette.json', 'M.json');
	}

	public function testModTestForProjectWithoutInterpreter()
	{
		unlink('Marvinette.json');
		rename('M.json', 'Marvinette.json');
		$this->defineStdin([
			'Y',
			'103',
			'100 15',
			'0',
			'head -n 120 | tail -n 10',
			'',
			'',
			'n',
			'Y',
			'n',
			'',
			'',
			''
		]);
		TestManager::modTest();
		$this->assertTrue(is_dir('tests/103'));
		$this->assertTrue(file_exists('tests/103/commandLineArguments'));
		$this->assertFalse(file_exists('tests/103/interpreterArguments'));
		$this->assertTrue(file_exists('tests/103/expectedReturnCode'));
		$this->assertTrue(file_exists('tests/103/stdoutFilter'));
		$this->assertFalse(file_exists('tests/103/stderrFilter'));
		$this->assertTrue(file_exists('tests/103/expectedStdout'));
		$this->assertFalse(file_exists('tests/103/expectedStderr'));
		$this->assertFalse(file_exists('tests/103/setup'));
		$this->assertFalse(file_exists('tests/103/stdinput'));
		$this->assertFalse(file_exists('tests/103/teardown'));

		$this->assertEquals(file_get_contents('tests/103/commandLineArguments'), '100 15');
		$this->assertEquals(file_get_contents('tests/103/expectedReturnCode'), '0');
		$this->assertEquals(file_get_contents('tests/103/stdoutFilter'), "head -n 120 | tail -n 10");
		FileManager::deleteFolder('tests/103');
	}
}
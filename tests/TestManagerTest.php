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
		$this->hideStdout();
		$this->defineStdin([
			'My Name',
			'README.md',
			'./',
			'',
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
		//$this->hideStdout();
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
}
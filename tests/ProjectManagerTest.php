<?php


require_once 'src/ProjectManager.php';
require_once 'tests/MarvinetteTestCase.php';

final class ProjectManagerTest extends MarvinetteTestCase
{
	public function testCreateProject(): void
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
		$project = new Project('Marvinette.json');

		$this->assertTrue(file_exists('Marvinette.json'));
		$this->assertEquals($project->name, 'My Name');
		$this->assertEquals($project->binaryName, 'README.md');
		$this->assertEquals($project->binaryPath, '.');
		$this->assertNull($project->interpreter->get());
		$this->assertEquals($project->testsFolder, 'tests');
	}

	public function testCreateProjectOverWrite(): void
	{
		$this->hideStdout();
		$this->defineStdin([
			'Y',
			'My Name',
			'README.md',
			'./',
			'',
			'',
			'n'
		]);
		ProjectManager::createProject();
		$project = new Project('Marvinette.json');

		$this->assertTrue(file_exists('Marvinette.json'));
		$this->assertEquals($project->name, 'My Name');
		$this->assertEquals($project->binaryName, 'README.md');
		$this->assertEquals($project->binaryPath, '.');
		$this->assertNull($project->interpreter->get());
		$this->assertEquals($project->testsFolder, 'tests');
	}

	public function testModProjectNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$this->assertFalse(ProjectManager::modProject());
		$this->expectOutputString("| Error\t|\tNo Configuration File Found!\n");
		rename('M.json', 'Marvinette.json');
	}

	public function testModProject(): void
	{
		$this->defineStdin([
			'',
			'main.php',
			'./src/',
			'php',
			'',
			'n',
		]);
		$this->hideStdout();
		$this->assertTrue(ProjectManager::modProject());
		$project = new Project('Marvinette.json');
		$this->assertTrue(file_exists('Marvinette.json'));
		$this->assertEquals($project->name->get(), 'My Name');
		$this->assertEquals($project->binaryName->get(), 'main.php');
		$this->assertEquals($project->binaryPath->get(), './src');
		$this->assertEquals($project->interpreter->get(), 'php');
		$this->assertEquals($project->testsFolder->get(), 'tests');
	}

	public function testDeleteProjectNoConfigFile(): void
	{
		rename('Marvinette.json', 'M.json');
		$this->assertFalse(ProjectManager::deleteProject());
		$this->expectOutputString("| Error\t|\tNo Configuration File Found!\n");
		rename('M.json', 'Marvinette.json');
	}


	public function testDeleteProjectStoppingBefore(): void
	{
		$this->hideStdout();
		mkdir('tests/101');
		touch('tests/101/stdinput');
		$this->defineStdin([
			'n', 'Y'
		]);
		$this->assertTrue(ProjectManager::deleteProject());
		$this->assertTrue(file_exists('tests/101/stdinput'));
		$this->assertTrue(file_exists('Marvinette.json'));
		FileManager::deleteFolder('tests/101');
	}

	public function testDeleteProjectDeletingTests(): void
	{
		$this->hideStdout();
		copy('Marvinette.json', 'Marvinette2.json');
		mkdir('tests/101');
		touch('tests/101/stdinput');
		$this->defineStdin([
			'Y', 'Y'
		]);
		$this->assertTrue(ProjectManager::deleteProject());
		$this->assertFalse(is_dir('tests/101'));
		$this->assertFalse(file_exists('Marvinette.json'));
		rename('Marvinette2.json', 'Marvinette.json');
	}

	public function testDeleteProjectNotDeletingTests(): void
	{
		$this->hideStdout();
		copy('Marvinette.json', 'Marvinette2.json');
		mkdir('tests/101');
		touch('tests/101/stdinput');
		$this->defineStdin([
			'Y', 'n'
		]);
		$this->assertTrue(ProjectManager::deleteProject());
		$this->assertTrue(file_exists('tests/101/stdinput'));
		$this->assertFalse(file_exists('Marvinette.json'));
		FileManager::deleteFolder('tests/101');
		rename('Marvinette2.json', 'Marvinette.json');
	}
}

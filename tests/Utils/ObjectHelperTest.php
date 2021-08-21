<?php

require_once 'tests/MarvinetteTestCase.php';

use PHPUnit\Framework\TestCase;

require_once 'src/Project.php';
require_once 'src/Utils/ObjectHelper.php';

final class ObjectHelperTest extends MarvinetteTestCase
{
	public function testObjectFieldIterator(): void
	{
		$project = new Project();
		$project->name->set('Hello');

		$expected = ['name', 'binaryName', 'binaryPath', 'interpreter', 'testsFolder'];
		$actual = [];
		ObjectHelper::forEachObjectField($project, function($fieldName, $value) use (&$actual) {
			$actual[$fieldName] = $value->get();
			return true;
		});
		$this->assertEquals($expected, array_keys($actual));
		$this->assertEquals(['Hello', null, '.', null, 'tests'], array_values($actual));
	}

	public function testObjectFieldIteratorOperateOnParameters(): void
	{
		$project = new Project();
		ObjectHelper::forEachObjectField($project, function($fieldName, $value) {
			$value->set($fieldName);
			return true;
		});
		foreach (get_object_vars($project) as $name => $field) {
			$this->assertEquals($name, $field->get());
		}
	}

	public function testPromptObjectFieldWithProjectObject(): Project
	{
		$this->defineStdinClone(['PROJECTNAME', 'BINARYNAME', 'PATH', 'python', 'tests']);
		$project = new Project();
		UserInterface::setTitle('');
		ObjectHelper::promptEachObjectField($project, function() {});
		$this->assertEquals($project->name->get(), 'PROJECTNAME');
		$this->assertEquals($project->binaryName->get(), 'BINARYNAME');
		$this->assertEquals($project->binaryPath->get(), 'PATH');
		$this->assertEquals($project->interpreter->get(), 'python');
		$this->assertEquals($project->testsFolder->get(), 'tests');
		return $project;
	}

	public function testPromptObjectFieldWithIgnoreField(): void
	{
		$this->defineStdinClone(['PROJECTNAME', 'BINARYNAME', 'tests']);
		$project = new Project();
		UserInterface::setTitle('');
		ObjectHelper::promptEachObjectField($project, function() {}, false, ['interpreter', 'binaryPath']);
		$this->assertEquals($project->name->get(), 'PROJECTNAME');
		$this->assertEquals($project->binaryName->get(), 'BINARYNAME');
		$this->assertEquals($project->binaryPath->get(), '.');
		$this->assertNull($project->interpreter->get());
		$this->assertEquals($project->testsFolder->get(), 'tests');
	}

	public function testPromptObjectFieldWithMultipleAttemps(): void
	{
		$this->hideStdout();
		$this->defineStdinClone(['', 'PROJECTNAME', 'tmp/end', '', 'BINARYNAME', 'PATH', 'python', 'tests']);
		$project = new Project();
		UserInterface::setTitle('');
		ObjectHelper::promptEachObjectField($project, function() {});
		$this->assertEquals($project->name->get(), 'PROJECTNAME');
		$this->assertEquals($project->binaryName->get(), 'BINARYNAME');
		$this->assertEquals($project->binaryPath->get(), 'PATH');
		$this->assertEquals($project->interpreter->get(), 'python');
		$this->assertEquals($project->testsFolder->get(), 'tests');
	}

	/**
	 * @depends ObjectHelperTest::testPromptObjectFieldWithProjectObject
	 */
	public function testPromptObjectFieldToModify(Project $project): void
	{
		$this->defineStdinClone(['', 'BINARYNAME2', 'PATH4', '', 'testers']);
		UserInterface::setTitle('');
		ObjectHelper::promptEachObjectField($project, function() {}, true);
		$this->assertEquals($project->name->get(), 'PROJECTNAME');
		$this->assertEquals($project->binaryName->get(), 'BINARYNAME2');
		$this->assertEquals($project->binaryPath->get(), 'PATH4');
		$this->assertEquals($project->interpreter->get(), 'python');
		$this->assertEquals($project->testsFolder->get(), 'testers');
	}
}
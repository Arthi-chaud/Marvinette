<?php

require_once 'src/Display/Color.php';
require_once 'src/Display/Displayer.php';
require_once 'src/ProjectManager.php';
require_once 'src/Exception/InvalidTestFolderException.php';

use Display\Color;

/**
 * Class holding functions to manage tests and their files
 */
class TestManager {

	public static function addTest(?Project $project = null)
	{
		UserInterface::setTitle("Add Test");
		if (!$project) {
			$project = new Project();
			$project->import(Project::ConfigurationFile);
		}
		$test = new Test();
		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			$helpMsg = $field->getPromptHelp();
			$help = $helpMsg ? " ($helpMsg)" : "";
			$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Blue)->displayText("Test's $cleanedFieldName$help: ", false);
		});
		$test->export($project);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}

	public static function modTest()
	{
		UserInterface::setTitle('Modify Test');
		$project = new Project();
		$project->import(Project::ConfigurationFile);
		$testsFolder = $project->testsFolder->get();
		$testName = self::selectTest($project);
		$test = new Test();
		$test->import(FileManager::normalizePath("$testsFolder/$testName"));

		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the test's new ". UserInterface::cleanCamelCase($fieldName), false);
			if ($field->getPromptHelp())
				UserInterface::$displayer->setColor(Color::Yellow)->displayText(' (' . $field->getPromptHelp() . ')', false);
			UserInterface::$displayer->setColor(Color::Yellow)->displayText( ', Leave empty if no change needed: ', false);
		}, true);
		foreach(get_object_vars($test) as $fieldName => $field) {
			if ($fieldName == 'name')
				continue;
			$fieldValue = $field->get();
			$fieldFileName = FileManager::normalizePath("$testsFolder/$testName/$fieldName");
			$fileExists = file_exists(FileManager::normalizePath($fieldFileName));
			if (is_bool($fieldValue)) {
				if ($fieldValue && !$fileExists)
					file_put_contents($fieldFileName, '');
				if (!$fieldValue && $fileExists)
					unlink($fieldFileName);
			} else {
				if (!is_null($fieldValue))
					file_put_contents($fieldFileName, $fieldValue);
				if (is_string($fieldValue) && !$fieldValue && $fileExists)
					unlink($fieldFileName);
			}
		}
		rename(FileManager::normalizePath("$testsFolder/$testName"), FileManager::normalizePath("$testsFolder/" . $test->name->get()));
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}
	
	public static function executeTest(string $testName, ?Project $project = null): bool
	{
		if (!$project) {
			$project = new Project();
			$project->import(Project::ConfigurationFile);
		}
		$test = new Test();
		$testPath = FileManager::normalizePath($project->testsFolder->get() . "/$testName");
		$test->import($testPath);
		return $test->execute($project);
	}

	public static function deleteTest(): void
	{
		UserInterface::setTitle("Delete Test");
		$project = new Project();
		$project->import(Project::ConfigurationFile);
		$testName = self::selectTest($project);

		FileManager::deleteFolder($project->testsFolder->get() . DIRECTORY_SEPARATOR . $testName);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Yellow)->displayText("The test '$testName' has been correctly deleted!");
		UserInterface::popTitle();
	}

	/**
	 * Checks if a folder is a valid test folder (using Test's object fields)
	 * @param string $path a path to what could be a test folder
	 * @return bool 
	 */
	protected static function folderIsATest(string $path): bool
	{
		$files = [];
		$testsFields = get_object_vars(new Test());
		$path = FileManager::normalizePath($path);
		$path = FileManager::removeEndDirSeparator($path);
		if (!is_dir($path))
			return false;
		$files = glob("$path/*");
		if ($files == [])
			return false;
		foreach ($files as $file) {
			if (!in_array(basename($file), array_keys($testsFields)))
				return false;
		}
		return true;	
	}

	/**
	 * @return array of string of valid tests folders
	 * @param string $testsFolder the path to a folder that can contain tests folders
	 * @param bool $fullPath if true the array contains tests names concatened with $testsFolder
	 */
	public static function getTestsFolders(string $testsFolder, bool $fullPath = false): array
	{
		$testsName = [];
		$files = glob(FileManager::normalizePath("$testsFolder/*"));
		foreach ($files as $file) {
			if (self::folderIsATest($file))
				$testsName[] = $fullPath ? $file : basename($file);
		}
		return $testsName;
	}
	
	public static function selectTest(Project $project): ?string
	{
		UserInterface::setTitle("Select a Test");
		$testsFolder = $project->testsFolder->get();
		$testsName = self::getTestsFolders($testsFolder);
		$testCount = count($testsName);
		if (!$testCount) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Red)->displayText("No tests available");
			throw new InvalidTestFolderException();
		}
		for ($i = 0; $i < $testCount; $i++) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Blue)->displayText("$i - " . basename($testsName[$i]));
			$choices[] = "$i";
		}
		$selected = UserInput::getOption(function () use ($testCount) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("Select a test (between 0 and " . ($testCount - 1) . '): ', false);
		}, $choices);
		UserInterface::popTitle();
		return basename($testsName[$selected]);
	}
}
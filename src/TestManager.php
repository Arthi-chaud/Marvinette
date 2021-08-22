<?php

require_once 'src/Display/Color.php';
require_once 'src/Display/Displayer.php';
require_once 'src/ProjectManager.php';
require_once 'src/Exception/InvalidTestFolderException.php';
require_once 'src/Exception/NoConfigFileException.php';

use Display\Color;

/**
 * Class holding functions to manage tests and their files
 */
class TestManager {

	public static function addTest(?Project $project = null)
	{
		UserInterface::setTitle("Add Test");
		if (!file_exists(Project::ConfigurationFile)) {
			throw new NoConfigFileException();
		}
		if (!$project) {
			$project = new Project(Project::ConfigurationFile);
		}
		$test = new Test();
		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			$helpMsg = $field->getPromptHelp();
			$help = $helpMsg ? " ($helpMsg)" : "";
			$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Blue)->displayText("Test's $cleanedFieldName$help: ", false);
		});
		$test->export($project->testsFolder->get());
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}

	public static function modTest()
	{
		UserInterface::setTitle('Modify Test');
		if (!file_exists(Project::ConfigurationFile)) {
			throw new NoConfigFileException();
		}
		$project = new Project(Project::ConfigurationFile);
		$testsFolder = $project->testsFolder->get();
		$testName = self::selectTest($project);
		$test = new Test(FileManager::normalizePath("$testsFolder/$testName"));

		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the test's new ". UserInterface::cleanCamelCase($fieldName), false);
			if ($field->getPromptHelp()) {
				UserInterface::$displayer->setColor(Color::Yellow)->displayText(' (' . $field->getPromptHelp() . ')', false);
			}
			UserInterface::$displayer->setColor(Color::Yellow)->displayText( ', Leave empty if no change needed: ', false);
		}, true);
		foreach(get_object_vars($test) as $fieldName => $field) {
			if ($fieldName == 'name') {
				continue;
			}
			$fieldValue = $field->get();
			$fieldFileName = FileManager::normalizePath("$testsFolder/$testName/$fieldName");
			$fileExists = file_exists(FileManager::normalizePath($fieldFileName));
			if (is_bool($fieldValue)) {
				if ($fieldValue && !$fileExists) {
					file_put_contents($fieldFileName, '');
				}
				if (!$fieldValue && $fileExists) {
					unlink($fieldFileName);
				}
			} else {
				if (!is_null($fieldValue)) {
					file_put_contents($fieldFileName, $fieldValue);
				}
				if (is_string($fieldValue) && !$fieldValue && $fileExists) {
					unlink($fieldFileName);
				}
			}
		}
		rename(FileManager::normalizePath("$testsFolder/$testName"), FileManager::normalizePath("$testsFolder/" . $test->name->get()));
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}
	
	public static function executeTest(?string $testName = null, ?Project $project = null): bool
	{
		$testStatus = true;
		if (!file_exists(Project::ConfigurationFile)) {
			throw new NoConfigFileException();
		}
		if (!$project) {
			$project = new Project(Project::ConfigurationFile);
		}
		if ($testName == null) {
			$testName = self::selectTest($project);
		}
		UserInterface::setTitle("Test '$testName'");
		$testPath = FileManager::normalizePath($project->testsFolder->get() . "/$testName");
		$test = new Test($testPath);
		try {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("Executing Test '$testName'...");
			$test->execute($project);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("$testName: Test passed!");
		} catch (Exception $e) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Red)->displayText("$testName: Test Failed! " . $e->getMessage());
			$diffOutputFile = FileManager::normalizePath(Test::TmpFileFolder . '/' . Test::TmpFilePrefix . Test::TmpDiffFilePrefix);
			if (file_exists($diffOutputFile)) {
				$diffContent = file_get_contents($diffOutputFile);
				foreach (explode("\n", $diffContent) as $diffLine) {
					UserInterface::displayTitle();
					UserInterface::$displayer->setColor(Color::Red)->displayText($diffLine);
				}
				unlink($diffOutputFile);
			}
			$testStatus = false;
		}
		UserInterface::popTitle();
		return $testStatus;
	}

	public static function executesAllTests(?Project $project = null): bool
	{
		UserInterface::setTitle("Executing");
		if (!file_exists(Project::ConfigurationFile)) {
			throw new NoConfigFileException();
		}
		if (!$project) {
			$project = new Project(Project::ConfigurationFile);
		}
		$failedTestCount = 0;
		$tests = self::getTestsFolders($project->testsFolder->get());
		if ($tests == []) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Red)->displayText("No tests available");
			throw new InvalidTestFolderException();
		}
		foreach ($tests as $testName) {
			$testStatus = self::executeTest($testName, $project);
			if ($testStatus) {
				$failedTestCount++;
			}
		}
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Default)->displayText("Test Count: ", false);
		UserInterface::$displayer->setColor(Color::Blue)->displayText(strval(count($tests)), false);
		UserInterface::$displayer->setColor(Color::Default)->displayText(" | Success: ", false);
		UserInterface::$displayer->setColor(Color::Green)->displayText(strval(count($tests) - $failedTestCount), false);
		UserInterface::$displayer->setColor(Color::Default)->displayText(" | Failed: ", false);
		UserInterface::$displayer->setColor(Color::Red)->displayText(strval($failedTestCount));
		UserInterface::popTitle();
		return $failedTestCount == 0;
	}

	public static function deleteTest(): void
	{
		UserInterface::setTitle("Delete Test");
		if (!file_exists(Project::ConfigurationFile)) {
			throw new NoConfigFileException();
		}
		$project = new Project(Project::ConfigurationFile);
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
	public static function folderIsATest(string $path): bool
	{
		$files = [];
		$testsFields = get_object_vars(new Test());
		$path = FileManager::normalizePath($path);
		$path = FileManager::removeEndDirSeparator($path);
		if (!is_dir($path)) {
			return false;
		}
		$files = glob("$path/*");
		if ($files == []) {
			return false;
		}
		foreach ($files as $file) {
			if (!in_array(basename($file), array_keys($testsFields))) {
				return false;
			}
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
			if (self::folderIsATest($file)) {
				$testsName[] = $fullPath ? $file : basename($file);
			}
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
			UserInterface::$displayer->setColor(Color::Yellow)->displayText("Select a test (between 0 and " . ($testCount - 1) . '): ', false);
		}, $choices);
		UserInterface::popTitle();
		return basename($testsName[$selected]);
	}
}
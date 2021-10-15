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
		if (!$project) {
			$project = new Project(Project::ConfigurationFile);
		}
		$test = new Test();
		$ignoredFields = [];
		if ($project->interpreter->get() == null) {
			$ignoredFields[] = 'interpreterArguments';
		}
		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			$helpMsg = $field->getPromptHelp();
			$help = $helpMsg ? " ($helpMsg)" : "";
			$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Blue)->displayText("Test's $cleanedFieldName$help: ", false);
		}, false, $ignoredFields);
		$test->export($project->testsFolder->get());
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}

	public static function createSampleTest(?string $name = null): bool
	{
		UserInterface::setTitle("Create Sample Test");
		$project = new Project(Project::ConfigurationFile);
		if ($name == null) {
			$test = new Test();
			$ignoredFields = array_slice(array_keys(get_object_vars($test)), 1);
			ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
				$helpMsg = $field->getPromptHelp();
				$help = $helpMsg ? " ($helpMsg)" : "";
				$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
				UserInterface::displayTitle();
				UserInterface::$displayer->setColor(Color::Blue)->displayText("Test's $cleanedFieldName$help: ", false);
			}, false, $ignoredFields);
			$name = $test->name->get();
		}
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("Generating Sample Tests Files...");
		Test::exportSample($project->testsFolder->get(), $name);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Green)->displayText("Done");
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
		if (is_null($testName)) {
			return true;
		}
		$test = new Test(FileManager::normalizePath("$testsFolder/$testName"));
		$ignoredFields = [];
		if ($project->interpreter->get() == null) {
			$ignoredFields[] = 'interpreterArguments';
		}
		ObjectHelper::promptEachObjectField($test, function ($fieldName, $field) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the test's new ". UserInterface::cleanCamelCase($fieldName), false);
			if ($field->getPromptHelp()) {
				UserInterface::$displayer->setColor(Color::Yellow)->displayText(' (' . $field->getPromptHelp() . ')', false);
			}
			UserInterface::$displayer->setColor(Color::Yellow)->displayText( ', Leave empty if no change needed: ', false);
		}, true, $ignoredFields);

		$jsonContent = [];
		foreach(get_object_vars($test) as $fieldName => $field) {
			if ($fieldName == 'name') {
				continue;
			}
			$fieldValue = $field->get();
			if (in_array($fieldName, ['expected' . Test::TmpFileStderrPrefix, 'expected' . Test::TmpFileStdoutPrefix, 'stdinput'])) {
				$fieldFileName = FileManager::normalizePath("$testsFolder/$testName/$fieldName");
				$fileExists = file_exists(FileManager::normalizePath($fieldFileName));
				if ($fileExists && $fieldValue == false)
					unlink($fieldFileName);
				else if (!$fileExists && $fieldValue == true)
					touch($fieldFileName);
			} else {
				$jsonContent[$fieldName] = $field->get();
			}
		}
		$outputConfig = FileManager::normalizePath("$testsFolder/$testName/" . Test::ConfigFile);
		unlink($outputConfig);
		file_put_contents($outputConfig, json_encode($jsonContent, JSON_PRETTY_PRINT));
		rename(FileManager::normalizePath("$testsFolder/$testName"), FileManager::normalizePath("$testsFolder/" . $test->name->get()));
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		UserInterface::popTitle();
		return true;
	}
	
	public static function executeTest(?string $testName = null, ?Project $project = null): ?bool
	{
		$testStatus = true;
		if (!$project) {
			$project = new Project(Project::ConfigurationFile);
		}
		if ($testName == null) {
			$testName = self::selectTest($project);
		}
		if (is_null($testName)) {
			return false;
		}
		UserInterface::setTitle("Test '$testName'");
		$testPath = FileManager::normalizePath($project->testsFolder->get() . "/$testName");
		$test = new Test($testPath);
		$binaryPath = FileManager::normalizePath($project->binaryPath . '/' . $project->binaryName);
		if (!file_exists($binaryPath)) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Red)->displayText("'$binaryPath': Project's file not found...");
			return null;
		}
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
		if ($project == null) {
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
			$testSucces = self::executeTest($testName, $project);
			if (is_null($testSucces) || !$testSucces) {
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
		$project = new Project(Project::ConfigurationFile);
		$testName = self::selectTest($project);
		if (is_null($testName)) {
			return;
		}
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
		return file_exists(FileManager::normalizePath("$path/" . Test::ConfigFile));
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
		$selectedIndex = null;
		if ($testCount > 1) {
			$selectedIndex = UserInput::getOption(function () use ($testCount) {
				UserInterface::displayTitle();
				UserInterface::$displayer->setColor(Color::Yellow)->displayText("Select a test (between 0 and " . ($testCount - 1) . '): ', false);
			}, $choices);
		} else {
			if (UserInput::getYesNoOption("Select this test ?", Color::Yellow)) {
				$selectedIndex = 0;
			}
		}
		UserInterface::popTitle();
		if (is_null($selectedIndex))
			return null;
		return basename($testsName[$selectedIndex]);
	}
}
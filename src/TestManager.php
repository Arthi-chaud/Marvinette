<?php

require_once 'src/Display/Color.php';
require_once 'src/Display/Displayer.php';
require_once 'src/ProjectManager.php';

use Display\Color;

/**
 * Class holding functions to manage tests and their files
 */
class TestManager {

	public static function addTest(?Project $project = null)
	{
		$displayFrameTitle = "Add Test";
		if (!$project) {
			$project = new Project();
			$project->import(Project::ConfigurationFile);
		}
		$test = new Test();
		foreach (get_object_vars($test) as $fieldName => $field) {
			for ($choosen = false; !$choosen; ) {
				$helpMsg = $field->getPromptHelp();
				$help = $helpMsg ? " ($helpMsg)" : "";
				$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
				UserInterface::displayCLIFrame($displayFrameTitle);
				UserInterface::$displayer->setColor(Color::Blue)->displayText("Test's $cleanedFieldName$help: ", false);
				if (($value = fgets(STDIN)) == null)
				return false;
				try {
					$test->$fieldName->set(rtrim($value));
					$choosen = true;
				} catch (Exception $e) {
					UserInterface::displayCLIFrame($displayFrameTitle);
					UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
				}
			}
		}
		$test->export($project);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		return true;
	}

	public static function modTest()
	{
		$displayFrameTitle = 'Modify Test';
		$project = new Project();
		$project->import(Project::ConfigurationFile);
		$testsFolder = $project->testsFolder->get();
		$testName = self::selectTest($project);
		$testTmp = new Test();
		$finalTest = new Test();
		$finalTest->import(FileManager::getCPPath("$testsFolder/$testName"));

		if ($testName == null)
			return false;
		$return = ObjectHelper::forEachObjectField($finalTest, function($_, $fieldName, $field) use ($displayFrameTitle, $finalTest, $testTmp) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the test's new ". UserInterface::cleanCamelCase($fieldName), false);
			if ($field->getPromptHelp())
				UserInterface::$displayer->setColor(Color::Yellow)->displayText(' (' . $field->getPromptHelp() . ')', false);
			UserInterface::$displayer->setColor(Color::Yellow)->displayText( ', Leave empty if no change needed: ', false);
			if (($value = fgets(STDIN)) == null)
				return null;
			$value = rtrim($value);
			if ($value == "")
				$value = $finalTest->$fieldName->get();
			try {
				$testTmp->$fieldName->set($value);
				return true;
			} catch (Exception $e) {
				UserInterface::displayCLIFrame($displayFrameTitle);
				UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
				return false;
			}
		});
		if (!$return)
			return false;
		foreach(get_object_vars($testTmp) as $fieldName => $field) {
			if ($fieldName == 'name')
				continue;
			$fieldValue = $field->get();
			$fieldFileName = FileManager::getCPPath("$testsFolder/$testName/$fieldName");
			$fileExists = file_exists(FileManager::getCPPath($fieldFileName));
			if (is_bool($fieldValue)) {
				if ($fieldValue && !$fileExists)
					file_put_contents($fieldFileName, '');
				if (!$fieldValue && $fileExists)
					unlink($fieldFileName);
			} else {
				if (!is_null($fieldValue))
					file_put_contents($fieldFileName, $fieldValue);
				if (!$fieldValue && $fileExists)
					unlink($fieldFileName);
			}
		}
		rename(FileManager::getCPPath("$testsFolder/$testName"), FileManager::getCPPath("$testsFolder/" . $testTmp->name->get()));
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Test's files are ready!");
		return true;
	}
	
	public static function executeTest(string $testName, ?Project $project = null): bool
	{
		if (!$project) {
			$project = new Project();
			$project->import(Project::ConfigurationFile);
		}
		$test = new Test();
		$testPath = FileManager::getCPPath($project->testsFolder->get() . "/$testName");
		$test->import($testPath);
		return $test->execute($project);
	}

	public static function deleteTest(): bool
	{
		$project = new Project();
		$project->import(Project::ConfigurationFile);
		$testName = self::selectTest($project);

		if ($testName == null)
			return false;
		FileManager::deleteFolder($project->testsFolder->get() . DIRECTORY_SEPARATOR . $testName);
		return true;
	}
	
	public static function selectTest(Project $project): ?string
	{
		$displayFrameTitle = "Select a Test";
		$testsFolder = $project->testsFolder->get();
		$testsNames = glob(FileManager::getCPPath("$testsFolder/*"));
		$testCount = count($testsNames);
		$choices = [];
		sort($testsNames);
		if ($testsNames == []) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Red)->displayText("No tests available");
			return null;
		}
		for ($i = 0; $i < $testCount; $i++) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Blue)->displayText("$i - " . basename($testsNames[$i]));
			$choices[] = "$i";
		}
		UserInterface::displayCLIFrame($displayFrameTitle, true);
		var_dump($choices);
		$selected = UserInput::getOption(function () use ($displayFrameTitle, $testCount) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Green)->displayText("Select a test (between 0 and " . ($testCount - 1) . '): ', false);
		}, $choices);

		if ($selected == null)
			return null;
		return basename($testsNames[$selected]);
	}
}
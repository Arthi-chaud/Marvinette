<?php

require_once 'src/Project.php';
require_once 'src/Display/Displayer.php';
require_once 'src/Utils/UserInput.php';
require_once 'src/Utils/UserInterface.php';
require_once 'Utils/CLIOption.php';
require_once 'src/Test.php';
require_once 'src/Utils/FileManager.php';

use Display\Color;

/**
 * @brief Object holding method where the main functions are
*/
class Marvinette
{

	const ConfigurationFile = "Marvinette.json";

	private function forEachObjectField(&$obj, callable $callable): bool
	{
		foreach (get_object_vars($obj) as $fieldName => $field)
			for ($choosen = false; !$choosen; ) {
				$choosen = $callable($obj, $fieldName, $field);
				if ($choosen == null)
					return false;
			}
		return true;
	}

	public function launch(): bool
	{
		$optionsCalls = [
			'create-project' => 'createProject',
			'del-project' => 'deleteProject',
			'mod-project' => 'modProject',
			'add-test' => 'addTest',
			'del-test' => 'deleteTest',
		];
		$options = CLIOption::get(array_keys($optionsCalls));
		foreach ($optionsCalls as $option => $call) {
			if (array_key_exists($option, $options)) {
				UserInterface::displayCLIFrame("Marvinette\t", true);
				return $this->$call();
			}
		}
		UserInterface::displayHelp();
		return false;
	}
	
	protected function createProject(): bool
	{
		$displayFrameTitle = "Create Project";
		if (file_exists(self::ConfigurationFile)) {
			if ($this->overWriteProject())
				unlink(self::ConfigurationFile);
			else
				return false;
		}
		$project = new Project();
		$this->forEachObjectField($project, function($project, $fieldName, $field) use ($displayFrameTitle) {
			$helpMsg = $field->getPromptHelp();
			$help = $helpMsg ? " ($helpMsg)" : "";
			$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Blue)->displayText("Enter the project's $cleanedFieldName$help: ", false);
			if (($value = fgets(STDIN)) == null)
				return null;
			try {
				$project->$fieldName->set(rtrim($value));
				return true;
			} catch (Exception $e) {
				UserInterface::displayCLIFrame($displayFrameTitle);
				UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
				return false;
			}
		});
		$project->export(Marvinette::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is created!");

		$addNow = UserInput::getYesNoOption($displayFrameTitle, "Would You Like to add a test now", Color::Blue);
		if ($addNow == 'Y')
			return $this->addTest($project);
		return true;
	}

	protected function displayNoConfigFileFound($displayFrameTitle): void
	{
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Red)->displayText("No Configuration File Found!");
	}

	protected function modProject(): bool
	{
		$displayFrameTitle = "Modify Project";
		if (!file_exists(self::ConfigurationFile)) {
			$this->displayNoConfigFileFound($displayFrameTitle);
			return false;
		}
		$project = new Project();
		
		$project->import(self::ConfigurationFile);
		$this->forEachObjectField($project, function($_, $fieldName, $field) use ($project, $displayFrameTitle) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the project's new ". UserInterface::cleanCamelCase($fieldName) . " ", false);
			UserInterface::$displayer->setColor(Color::Yellow)->displayText("(Leave empty if no change needed): ", false);
			if (($value = fgets(STDIN)) == null)
				return null;
			$value = rtrim($value);
			if ($value == "")
				$value = $project->$fieldName->get();
			try {
				$project->$fieldName->set($value);
				return true;
			} catch (Exception $e) {
				UserInterface::displayCLIFrame($displayFrameTitle);
				UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
				return false;
			}
		});
		unlink(self::ConfigurationFile);
		$project->export(self::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is updated!");
		$addNow = UserInput::getYesNoOption($displayFrameTitle, "Would You Like to add a test now", Color::Blue);
		if ($addNow == 'Y')
			return $this->addTest($project);
		return true;
	}

	
	protected function deleteProject(): bool
	{
		$displayFrameTitle = "Delete Project";
		if (!file_exists(self::ConfigurationFile)) {
			$this->displayNoConfigFileFound($displayFrameTitle);
			return false;
		}
		$project = new Project();
		$project->import(self::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Red)->displayText("Warning: You are about to delete your configuration file");
		$delete = UserInput::getYesNoOption($displayFrameTitle, "Do you want to continue?", Color::Red);
		if ($delete == 'Y')
		unlink(self::ConfigurationFile);
		else {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file has not been deleted!");
			return false;
		}
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is deleted!");
		$delete = UserInput::getYesNoOption($displayFrameTitle, "Do you want to delete your tests?", Color::Red);
		if ($delete == 'Y') {
			FileManager::deleteFolder($project->testsFolder->get());
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's tests file are deleted!");
			return false;
		}
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's tests file are not deleted!");
		return true;
	}
	
	protected function overwriteProject(): bool
	{
		$displayFrameTitle = "Existing Project";
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Red)->displayText("Warning:");
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Blue)->displayText("A configuration file already exists");
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Blue)->displayText("Creating a new project will overwrite this file");
		$overwrite = UserInput::getYesNoOption($displayFrameTitle, "Do you want to continue?", Color::Red);
		if ($overwrite == 'Y')
		return true;
		return false;
	}
	
	protected function addTest(?Project $project = null)
	{
		$displayFrameTitle = "Add Test";
		if (!$project) {
			$project = new Project();
			$project->import(self::ConfigurationFile);
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
	
	protected function executeTest(Project $project, string $testName): bool
	{
		$testPath = FileManager::getCPPath($project->testsFolder->get() . "/$testName");
		
		$expectedReturnCode = null;
		$commandLineArgs = "";
		$interpreter = $project->interpreter->get();
		if (!is_dir($testPath))
			throw new Exception('Invalid Test Path');
		if (file_exists(FileManager::getCPPath("$testPath/expectedReturnCode")))
			$expectedReturnCode = intval(file_get_contents(FileManager::getCPPath("$testPath/expectedReturnCode")));
		if (file_exists(FileManager::getCPPath("$testPath/commandLineArguments")))
			$commandLineArgs = file_get_contents(FileManager::getCPPath("$testPath/commandLineArguments"));
		$returnCode = 0;
		if (file_exists(FileManager::getCPPath("$testPath/setup"))) {
			system(file_get_contents(FileManager::getCPPath("$testPath/setup")), $returnCode);
			if ($returnCode != 0)
			throw new Exception("Test's setup failed. Return code: $returnCode");
		}
		$command = $project->binaryPath->get() . DIRECTORY_SEPARATOR . $project->binaryName->get() . ' ' . $commandLineArgs;
		if ($interpreter != null)
			$command = "$interpreter $command";
		system($command . "> tmp/MarvinetteStdout 2> tmp/MarvinetteStderr", $returnCode);
		if ($expectedReturnCode != null && $expectedReturnCode != $returnCode)
			throw new Exception("The program didn't return the expected code. Expected: $returnCode, actual: $expectedReturnCode");
		if (file_exists(FileManager::getCPPath("$testPath/stdoutFilter"))) {
			$stdoutFilterCommand = file_get_contents(FileManager::getCPPath("$testPath/stdoutFilter"));
			system("cat tmp/MarvinetteStdout | $stdoutFilterCommand > tmp/MarvinetteFilteredStdout", $returnCode);
			if ($returnCode != 0)
				throw new Exception("Test's stdout filtering failed. Return code: $returnCode");
			system("cat tmp/MarvinetteFilteredStdout > tmp/MarvinetteStdout");
		}
		if (file_exists(FileManager::getCPPath("$testPath/stderrFilter"))) {
			$stderrFilterCommand = file_get_contents(FileManager::getCPPath("$testPath/stderrFilter"));
			system("cat tmp/MarvinetteStderr | $stderrFilterCommand > tmp/MarvinetteFilteredStderr", $returnCode);
			if ($returnCode != 0)
				throw new Exception("Test's stderr filtering failed. Return code: $returnCode");
			system("cat tmp/MarvinetteFilteredStderr > tmp/MarvinetteStderr");
		}
		if (file_exists(FileManager::getCPPath("$testPath/expectedStdout"))) {
			$expectedStdoutFile = FileManager::getCPPath("$testPath/expectedStdout");
			system("diff $expectedStdoutFile tmp/MarvinetteStdout", $returnCode);
			if ($returnCode != 0)
				return false;
		}
		if (file_exists(FileManager::getCPPath("$testPath/expectedStderr"))) {
			$expectedStderrFile = FileManager::getCPPath("$testPath/expectedStderr");
			system("diff $expectedStderrFile tmp/MarvinetteStderr", $returnCode);
			if ($returnCode != 0)
				return false;
		}
		if (file_exists(FileManager::getCPPath("$testPath/teardown"))) {
			system(file_get_contents(FileManager::getCPPath("$testPath/teardown")), $returnCode);
			if ($returnCode != 0)
				throw new Exception("Test's teardown failed. Return code: $returnCode");
		}
		return true;
	}

	protected function deleteTest(): bool
	{
		$project = new Project();
		$project->import(self::ConfigurationFile);
		$testName = $this->selectTest($project);

		if ($testName == null)
			return false;
		FileManager::deleteFolder($project->testsFolder->get() . DIRECTORY_SEPARATOR . $testName);
	}
	
	protected function selectTest(Project $project): ?string
	{
		$displayFrameTitle = "Select a Test";
		$testsFolder = $project->testsFolder->get();
		$testsNames = glob(FileManager::getCPPath("$testsFolder/*"));
		$testCount = count($testsNames);
		sort($testsNames);
		if ($testsNames == []) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Red)->displayText("No tests available");
			return null;
		}
		for ($i = 0; $i < $testCount; $i++) {
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor(Color::Blue)->displayText("$i - " . $testsNames[$i]);
		}
		UserInterface::displayCLIFrame($displayFrameTitle, true);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Green)->displayText("Select a test (between 0 and " . ($testCount - 1) . ')', false);
		$selected = UserInput::getOption("Select a test (between 0 and " . ($testCount - 1) . ')', range(0, $testCount));
		if ($selected == null)
			return null;
		return $testsNames[$i];
	}
}
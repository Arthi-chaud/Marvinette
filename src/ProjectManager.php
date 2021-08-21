<?php

require_once 'src/Project.php';
require_once 'src/Display/Displayer.php';
require_once 'src/Utils/UserInput.php';
require_once 'src/Utils/UserInterface.php';
require_once 'src/Utils/CommandLine.php';
require_once 'src/Test.php';
require_once 'src/TestManager.php';
require_once 'src/Utils/FileManager.php';
require_once 'src/Utils/ObjectHelper.php';
require_once 'src/Exception/EndOfFileException.php';

use Display\Color;

/**
 * Object holding method where the main functions are
*/
class ProjectManager
{

	public static function createProject(): bool
	{
		UserInterface::setTitle("Create Project");
		if (file_exists(Project::ConfigurationFile)) {
			if (self::promptOverWriteProject()) {
				unlink(Project::ConfigurationFile);
			} else {
				return true;
			}
		}
		$project = new Project();
		ObjectHelper::promptEachObjectField($project, function ($fieldName, $field) {
			$helpMsg = $field->getPromptHelp();
			$help = $helpMsg ? " ($helpMsg)" : "";
			$cleanedFieldName = UserInterface::cleanCamelCase($fieldName);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Blue)->displayText("Enter the project's $cleanedFieldName$help: ", false);
		});
		$project->export(Project::ConfigurationFile);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is created!");
		if (self::promptAddTest()) {
			return TestManager::addTest($project);
		}
		UserInterface::popTitle();
		return true;
	}

	public static function promptAddTest(): bool
	{
		return UserInput::getYesNoOption("Would You Like to add a test now", Color::Blue);
	}

	public static function displayNoConfigFileFound(): void
	{
		UserInterface::setTitle("Error");
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Red)->displayText("No Configuration File Found!");
		UserInterface::popTitle();
	}

	public static function modProject(): bool
	{
		UserInterface::setTitle("Modify Project");
		if (!file_exists(Project::ConfigurationFile)) {
			self::displayNoConfigFileFound();
			return false;
		}
		$project = new Project(Project::ConfigurationFile);

		ObjectHelper::promptEachObjectField($project,function ($fieldName, $field) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the project's new ". UserInterface::cleanCamelCase($fieldName) . " ", false);
			UserInterface::$displayer->setColor(Color::Yellow)->displayText("(Leave empty if no change needed): ", false);
		}, true);
		unlink(Project::ConfigurationFile);
		$project->export(Project::ConfigurationFile);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is updated!");
		
		if (self::promptAddTest()) {
			return TestManager::addTest($project);
		}
		UserInterface::popTitle();
		return true;
	}

	
	public static function deleteProject(): bool
	{
		UserInterface::setTitle("Delete Project");
		if (!file_exists(Project::ConfigurationFile)) {
			self::displayNoConfigFileFound();
			return false;
		}
		$project = new Project(Project::ConfigurationFile);
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Red)->displayText("Warning: You are about to delete your configuration file");
		$delete = UserInput::getYesNoOption("Do you want to continue?", Color::Red);
		if ($delete) {
			unlink(Project::ConfigurationFile);
		} else {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file has not been deleted!");
			UserInterface::popTitle();
			return true;
		}
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is deleted!");
		$delete = UserInput::getYesNoOption("Do you want to delete your tests?", Color::Red);
		if ($delete) {
			foreach(TestManager::getTestsFolders($project->testsFolder->get(), true) as $test)
				FileManager::deleteFolder($test);
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's tests file are deleted!");
			UserInterface::popTitle();
		} else {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's tests file are not deleted!");
			UserInterface::popTitle();
		}
		return true;
	}
	
	public static function promptOverwriteProject(): bool
	{
		UserInterface::setTitle("Existing Project");
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Red)->displayText("Warning:");
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Blue)->displayText("A configuration file already exists");
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Color::Blue)->displayText("Creating a new project will overwrite this file");
		$overwrite = UserInput::getYesNoOption("Do you want to continue?", Color::Red);
		UserInterface::popTitle();
		return $overwrite;
	}
}
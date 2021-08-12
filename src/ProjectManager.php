<?php

require_once 'src/Project.php';
require_once 'src/Display/Displayer.php';
require_once 'src/Utils/UserInput.php';
require_once 'src/Utils/UserInterface.php';
require_once 'Utils/CLIOption.php';
require_once 'src/Test.php';
require_once 'src/Utils/FileManager.php';
require_once 'Utils/ObjectHelper.php';

use Display\Color;

/**
 * @brief Object holding method where the main functions are
*/
class ProjectManager
{

	public static function createProject(): bool
	{
		$displayFrameTitle = "Create Project";
		if (file_exists(Project::ConfigurationFile)) {
			if (self::overWriteProject())
				unlink(Project::ConfigurationFile);
			else
				return false;
		}
		$project = new Project();
		ObjectHelper::forEachObjectField($project, function($project, $fieldName, $field) use ($displayFrameTitle) {
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
		$project->export(Project::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is created!");

		$addNow = UserInput::getYesNoOption($displayFrameTitle, "Would You Like to add a test now", Color::Blue);
		if ($addNow == 'Y')
			return TestManager::addTest($project);
		return true;
	}

	public static function displayNoConfigFileFound($displayFrameTitle): void
	{
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Red)->displayText("No Configuration File Found!");
	}

	public static function modProject(): bool
	{
		$displayFrameTitle = "Modify Project";
		if (!file_exists(Project::ConfigurationFile)) {
			self::displayNoConfigFileFound($displayFrameTitle);
			return false;
		}
		$project = new Project();
		
		$project->import(Project::ConfigurationFile);
		ObjectHelper::forEachObjectField($project, function($_, $fieldName, $field) use ($project, $displayFrameTitle) {
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
		unlink(Project::ConfigurationFile);
		$project->export(Project::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is updated!");
		$addNow = UserInput::getYesNoOption($displayFrameTitle, "Would You Like to add a test now", Color::Blue);
		if ($addNow == 'Y')
			return TestManager::addTest($project);
		return true;
	}

	
	public static function deleteProject(): bool
	{
		$displayFrameTitle = "Delete Project";
		if (!file_exists(Project::ConfigurationFile)) {
			self::displayNoConfigFileFound($displayFrameTitle);
			return false;
		}
		$project = new Project();
		$project->import(Project::ConfigurationFile);
		UserInterface::displayCLIFrame($displayFrameTitle);
		UserInterface::$displayer->setColor(Color::Red)->displayText("Warning: You are about to delete your configuration file");
		$delete = UserInput::getYesNoOption($displayFrameTitle, "Do you want to continue?", Color::Red);
		if ($delete == 'Y')
		unlink(Project::ConfigurationFile);
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
	
	public static function overwriteProject(): bool
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
}
<?php

require_once 'src/Project.php';
require_once 'src/Display/Displayer.php';
require_once 'src/Utils/UserInput.php';
require_once 'src/Utils/UserInterface.php';
require_once 'Utils/CLIOption.php';

use Display\Color;

/**
 * @brief Object holding method where the main functions are
*/
class Marvinette
{

    const ConfigurationFile = "Marvinette.json";

    public function launch(): bool
    {
        $optionsCalls = [
            'create-project' => 'createProject',
            'del-project' => 'deleteProject',
            'mod-project' => 'modProject',
            'add-test' => 'addTest',
            'mod-test' => 'modTest',
            'del-test' => 'deleteTest',
            'help' => 'displayHelp',
            'h' => 'displayHelp',
        ];
        $options = CLIOption::get(array_keys($optionsCalls));
        foreach ($optionsCalls as $option => $call) {
            if (array_key_exists($option, $options))
                return $this->$call();
        }
        UserInterface::displayHelp();
        return false;
    }

    protected function displayNoConfigFileFound($displayFrameTitle): void
    {
        UserInterface::displayCLIFrame($displayFrameTitle);
        UserInterface::$displayer->setColor(Color::Red)->displayText("No Configuration File Found!\n");
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
        foreach (get_object_vars($project) as $fieldName => $_) {
            UserInterface::displayCLIFrame($displayFrameTitle);
            UserInterface::$displayer->setColor(Color::Green)->displayText("Enter the project's new ". ucwords($fieldName) . ":");
            UserInterface::displayCLIFrame($displayFrameTitle);
            UserInterface::$displayer->setColor(Color::Yellow)->displayText("(Leave empty if no change needed)");
            if (($value = fgets(STDIN)) == null)
                return false;
            $value = rtrim($value);
            if ($value != "")
                $project->$$fieldName = $value;
        }
        unlink(self::ConfigurationFile);
        $project->export(self::ConfigurationFile);
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
        UserInterface::$displayer->setColor(Color::Red)->displayText("Warning: You Are about to delete all your configuration file");
        $delete = UserInput::getYesNoOption($displayFrameTitle, "Do you want to continue?", Color::Red);
        if ($delete == 'Y')
            unlink(self::ConfigurationFile);
        else
            return false;
        $delete = $delete = UserInput::getYesNoOption($displayFrameTitle, "Do you want to delete your tests?", Color::Red);
        if ($delete == 'Y') {
            //todo remove folder
        }
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
        $overwrite = UserInput::getOption(function () use ($displayFrameTitle)
        {
            UserInterface::displayCLIFrame($displayFrameTitle);
            UserInterface::$displayer->setColor(Color::Red)->displayText("Do you want to continue? [Y/n]: ", false);
        }, ['Y', 'n']);
        if ($overwrite == 'Y')
            return true;
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
        foreach (get_object_vars($project) as $fieldName => $field) {
            for ($choosen = false; !$choosen; ) {
                $helpMsg = $field->getPromptHelp();
                $help = $helpMsg ? " ($helpMsg)" : "";
                $cleanedFieldName = mb_convert_case($fieldName, MB_CASE_LOWER);
                UserInterface::displayCLIFrame($displayFrameTitle);
                UserInterface::$displayer->setColor(Color::Blue)->displayText("Enter the project's $cleanedFieldName$help: ", false);
                if (($value = fgets(STDIN)) == null)
                    return false;
                try {
                    $project->$fieldName->set(rtrim($value));
                    $choosen = true;
                } catch (Exception $e) {
                    UserInterface::displayCLIFrame($displayFrameTitle);
                    UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
                }
            }
        }
        $project->export(Marvinette::ConfigurationFile);
        UserInterface::displayCLIFrame($displayFrameTitle);
        UserInterface::$displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is created!");
        return true;
    }
}
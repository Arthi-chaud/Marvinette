<?php

require_once 'src/Project.php';
require_once 'src/Display/Displayer.php';

use Display\Displayer;
use Display\Color;

/**
 * @brief Object holding method where the main functions are
*/
class Marvinette {

    public function __construct()
    {
        $this->displayer = new Displayer();
    }

    const ConfigurationFile = "Marvinette.json";

    protected Displayer $displayer;

    protected function displayCLIFrame(string $text): self
    {
        $this->displayer->setColor(Color::Green)
                        ->displayText("| $text |\t");
        return $this;
    }

    protected function getOption(callable $questionPrompt, $options): ?string
    {
        $questionPrompt();
        while ($line = fgets(STDIN))
        {
            if (in_array($line, $options))
                return $line;
            $questionPrompt();
        }
        return null;
    }

    protected function displayNoConfigFileFound($displayFrameTitle): void
    {
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Red)->displayText("No Configuration File Found!\n");
    }


    public function modProject(): bool
    {
        $displayFrameTitle = "Modify Project";
        if (!file_exists(self::ConfigurationFile)) {
            $this->displayNoConfigFileFound($displayFrameTitle);
            return false;
        }
        $project = new Project();
        $project->import(self::ConfigurationFile);
        foreach (Project::Fields as $field) {
            $this->displayCLIFrame($displayFrameTitle)
                 ->displayer->setColor(Color::Green)->displayText("Enter the project's new ". ucwords($displayFrameTitle) . ":");
            $this->displayCLIFrame($displayFrameTitle)
                 ->displayer->setColor(Color::Yellow)->displayText("(Leave empty if no change needed)");
            if (($value = fgets(STDIN)) == null)
                return false;
            $object[$displayFrameTitle] = $value;
        }
        $project->setName($object['name'] ? $object['name'] : $project->getName())
			 ->setBinaryPath($object['binary path'] ? $object['binary path'] : $project->getBinaryPath())
			 ->setBinaryName($object['binary name'] ? $object['binary name'] : $project->getBinaryName())
			 ->setInterpreter($object['interpreter'] ? $object['interpreter'] : $project->getInterpreter())
			 ->setTestsFolder($object['tests folder'] ? $object['tests folder'] : $project->getTestsFolder());
        unlink(self::ConfigurationFile);
        $project->export(self::ConfigurationFile);
        return true;
    }

    public function deleteProject(): bool
    {
        $displayFrameTitle = "Delete Project";
        if (!file_exists(self::ConfigurationFile)) {
            $this->displayNoConfigFileFound($displayFrameTitle);
            return false;
        }
        $project = new Project();
        $project->import(self::ConfigurationFile);
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Red)->displayText("Warning: You Are about to delete all your configuration file");
        $delete = $this->getOption(function () use ($displayFrameTitle)
        {
            $this->displayCLIFrame($displayFrameTitle)
                ->displayer->setColor(Color::Red)->displayText("Do you want to continue? [Y/n]");
        }, ['Y', 'n']);
        if ($delete == 'Y')
            unlink(self::ConfigurationFile);
        else
            return false;
        $delete = $this->getOption(function () use ($displayFrameTitle)
        {
            $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Yellow)->displayText("Do you want to delete your tests?");
        }, ['Y', 'n']);
        if ($delete == 'Y') {
            $testsFolder = $project->getTestsFolder();
            array_map('unlink', glob("$testsFolder/*.*"));
            rmdir($testsFolder);
        }
        return true;
    }

    public function overwriteProject(): bool
    {
        $displayFrameTitle = "Create Project";
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Red)->displayText("Warning:");
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Blue)->displayText("A configuration file already exists");
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Blue)->displayText("Creating a new project will overwrite this file");
        $overwrite = $this->getOption(function () use ($displayFrameTitle)
        {
            $this->displayCLIFrame($displayFrameTitle)
                ->displayer->setColor(Color::Red)->displayText("Do you want to continue? [Y/n]");
        }, ['Y', 'n']);
        if ($overwrite == 'Y')
            return true;
        return false;
    }

    public function createProject(): bool
    {
        $displayFrameTitle = "Create Project";
        if (file_exists(self::ConfigurationFile)) {
            if ($this->overWriteProject())
                unlink(self::ConfigurationFile);
            else
                return false;
        }
        //todo create project
        return true;
    }
}
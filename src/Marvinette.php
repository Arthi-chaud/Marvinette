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

    protected function getOptions(): array
    {
        $shortopt = "h";
        $longopts = [
            'create-project',
            'del-project',
            'mod-project',
            'add-test',
            'mod-test',
            'del-test',
            'help',
        ];
        return getopt($shortopt, $longopts);
    }

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
        $options = $this->getOptions();
        foreach ($optionsCalls as $option => $call) {
            if (array_key_exists($option, $options))
                return $this->$call();
        }
        $this->displayHelp();
        return false;
    }

    protected function displayHelp(): bool
    {
        echo "marvinette [option]\n";
        echo "\toption:
        --create-project: Create a main configuration file, required to make tests
        --del-project: Delete configuration file and existing tests
        --mod-project: Modify the project's info.\n
        --add-test: Create a functionnal test
        --mod-test: Modify/Change an existing functionnal test\n
        --del-test: Delete a functionnal test
        -h, --help: display this usage\n";
        return true;
    }

    protected function displayCLIFrame(string $text): self
    {
        $this->displayer->setColor(Color::Green)
                        ->displayText("| $text |\t", false);
        return $this;
    }

    protected function getOption(callable $questionPrompt, $options): ?string
    {
        $questionPrompt();
        while ($line = fgets(STDIN))
        {
            $line = rtrim($line);
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
            $this->displayCLIFrame($displayFrameTitle)
                 ->displayer->setColor(Color::Green)->displayText("Enter the project's new ". ucwords($fieldName) . ":");
            $this->displayCLIFrame($displayFrameTitle)
                 ->displayer->setColor(Color::Yellow)->displayText("(Leave empty if no change needed)");
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
        $this->displayCLIFrame($displayFrameTitle)
             ->displayer->setColor(Color::Red)->displayText("Warning: You Are about to delete all your configuration file");
        $delete = $this->getOption(function () use ($displayFrameTitle)
        {
            $this->displayCLIFrame($displayFrameTitle)
                ->displayer->setColor(Color::Red)->displayText("Do you want to continue? [Y/n]: ", false);
        }, ['Y', 'n']);
        if ($delete == 'Y')
            unlink(self::ConfigurationFile);
        else
            return false;
        $delete = $this->getOption(function () use ($displayFrameTitle)
        {
            $this->displayCLIFrame($displayFrameTitle)
                 ->displayer->setColor(Color::Yellow)->displayText("Do you want to delete your tests?: ", false);
        }, ['Y', 'n']);
        if ($delete == 'Y') {
            //todo remove folder
        }
        return true;
    }

    protected function overwriteProject(): bool
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
                ->displayer->setColor(Color::Red)->displayText("Do you want to continue? [Y/n]: ", false);
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
                $help = $field->getPromptHelp();
                if ($help)
                    $help = " ($help)";
                else
                    $help = "";
                $this->displayCLIFrame($displayFrameTitle)
                     ->displayer->setColor(Color::Blue)->displayText("Enter the project's $fieldName$help: ", false);
                if (($value = fgets(STDIN)) == null)
                    return false;
                try {
                    $project->$fieldName->set(rtrim($value));
                    $choosen = true;
                } catch (Exception $e) {
                    $this->displayCLIFrame($displayFrameTitle)
                        ->displayer->setColor(Color::Red)->displayText($e->getMessage());
                }
            }
        }
        $project->export(Marvinette::ConfigurationFile);
        $this->displayCLIFrame($displayFrameTitle)
            ->displayer->setColor(Color::Cyan)->displayText("The Project's configuration file is created!");
        return true;
    }
}
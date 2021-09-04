#!/bin/php
<?php

set_include_path(dirname(realpath(__DIR__)));

require_once 'src/Exception/MarvinetteException.php';
require_once 'src/ProjectManager.php';
require_once 'src/TestManager.php';

function launch(): bool
{
	$optionsCalls = [
		'create-project' => [ProjectManager::class,'createProject'],
		'del-project' => [ProjectManager::class,'deleteProject'],
		'delete-project' => [ProjectManager::class,'deleteProject'],
		'mod-project' => [ProjectManager::class,'modProject'],
	
		'add-test' => [TestManager::class, 'addTest'],
		'create-test' => [TestManager::class, 'addTest'],
		'del-test' => [TestManager::class, 'deleteTest'],
		'delete-test' => [TestManager::class, 'deleteTest'],
		'mod-test' => [TestManager::class, 'modTest'],

		'execute-test' => [TestManager::class, 'executeTest'],
		'exec-test' => [TestManager::class, 'executeTest'],
		'execute-tests' => [TestManager::class, 'executesAllTests'],
		'exec-all' => [TestManager::class, 'executesAllTests'],

		'h' => [UserInterface::class, 'displayHelp'],
		'help' => [UserInterface::class, 'displayHelp'],
	];
	$options = CommandLine::getArguments(array_keys($optionsCalls));
	foreach ($optionsCalls as $option => $call) {
		if (array_key_exists($option, $options)) {
			return boolval(call_user_func($call));
		}
	}
	UserInterface::displayHelp();
	return false;
}

if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
	$returnCode = 0;
	try {
		UserInterface::setTitle("Marvinette", true);
		$returnCode = launch() ? 0 : 1;
	} catch (NoConfigFileException $e) {
		ProjectManager::displayNoConfigFileFound();
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Display\Color::Red)->displayText("Exiting...");
		$returnCode = 1;
	} catch (MarvinetteException $e) {
		UserInterface::displayTitle();
		UserInterface::$displayer->setColor(Display\Color::Red)->displayText("Exiting...");
		$returnCode = 1;
	}
	exit($returnCode);
}
exit($returnCode);
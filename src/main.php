#!/bin/php
<?php

require_once 'src/Exception/MarvinetteException.php';
require_once 'src/ProjectManager.php';
require_once 'src/TestManager.php';

function launch(): void
{
	$optionsCalls = [
		'create-project' => [ProjectManager::class,'createProject'],
		'del-project' => [ProjectManager::class,'deleteProject'],
		'mod-project' => [ProjectManager::class,'modProject'],
		'add-test' => [TestManager::class, 'addTest'],
		'del-test' => [TestManager::class, 'deleteTest'],
		'mod-test' => [TestManager::class, 'modTest']
	];
	$options = CommandLine::getArguments(array_keys($optionsCalls));
	foreach ($optionsCalls as $option => $call) {
		if (array_key_exists($option, $options)) {
			UserInterface::setTitle("Marvinette\t", true);
			call_user_func($call);
			return;
		}
	}
	UserInterface::displayHelp();
}

if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
	try {
		return launch();
	} catch (MarvinetteException $e) {
		echo "\nExiting...\n";
		return 1;
	}
}
return 1;
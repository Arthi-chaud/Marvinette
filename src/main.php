#!/bin/php
<?php

require_once 'src/Exception/MarvinetteException.php';
require_once 'src/ProjectManager.php';
require_once 'src/TestManager.php';

function launch(): void
{
	$optionsCalls = [
		'create-project' => ['ProjectManager','createProject'],
		'del-project' => ['ProjectManager','deleteProject'],
		'mod-project' => ['ProjectManager','modProject'],
		'add-test' => ['TestManager', 'addTest'],
		'del-test' => ['TestManager', 'deleteTest'],
		'mod-test' => ['TestManager', 'modTest']
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
		echo "Exiting...\n";
		return 1;
	}
}
return 1;
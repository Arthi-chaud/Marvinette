<?php

function install()
{
	if (posix_getuid() != 0)
		throw new Exception("Please execute this script using sudo");
	$CWD = getcwd();

	if (!$CWD)
		throw new Exception('Impossible to get current working directory');
	$PATH = getenv('PATH');
	if (!$PATH|| $PATH == [] || $PATH == '')
		throw new Exception("Impossible to access 'PATH' variable");
	$scriptPath = '/usr/bin';
	if (substr($scriptPath, -1, 1) != DIRECTORY_SEPARATOR)
		$scriptPath .= DIRECTORY_SEPARATOR;
	file_put_contents($scriptPath . 'marvinette', getScriptContent($CWD));
	chmod($scriptPath . 'marvinette', 0777);
}

function getScriptContent(string $projectPath): string
{
	if (substr($projectPath, -1, 1) != DIRECTORY_SEPARATOR)
		$projectPath .= DIRECTORY_SEPARATOR;
	$content = [
		"#!/bin/sh",
		'php -d include_path=' . $projectPath . ' ' . $projectPath . 'src/main.php $@',
		'exit $?'
	];
	return implode("\n", $content);
	
}

try {
	install();
	echo "Marvinette is installed!\n";
} catch (Exception $e) {
	echo "An error occured: " . $e->getMessage() . "\n";
	echo "Exiting...\n";
	exit(1);
}
exit(0);
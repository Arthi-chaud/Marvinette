<?php

function install()
{
	$isWindows = sys_get_temp_dir() == '/tmp';
	$CWD = getcwd();
	if (!$CWD)
		throw new Exception('Impossible to get current working directory');
	$HOME = getenv('HOME');
	if (!$HOME|| $HOME == [] || $HOME == '')
		throw new Exception("Impossible to access 'HOME' variable");
	if (!$isWindows) {
		$scriptPath = '$HOME/bin/';
	} else {
		$scriptPath = 'somewhere in path';
	}
	file_put_contents($scriptPath . 'marvinette', getScriptContent($CWD, $isWindows));
	chmod($scriptPath . 'marvinette', 0777);
}

function getScriptContent(string $projectPath, bool $isWindows = false): string
{
	if (substr($projectPath, -1, 1) != DIRECTORY_SEPARATOR)
		$projectPath .= DIRECTORY_SEPARATOR;
	if (!$isWindows) {
		$content = [
			"#!/bin/sh",
			'php  ' . $projectPath . 'src/main.php $@',
			'exit $?'
		];
	} else {
		$content = [
			"Invoke-Expression \"php $projectPath" . 'src/main.php $args"',
			'return', 
		];
	}
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
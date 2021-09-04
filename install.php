<?php

$isWindows = sys_get_temp_dir() != '/tmp';

function install()
{
	global $isWindows;
	$CWD = getcwd();
	if (!$CWD)
		throw new Exception('Impossible to get current working directory');
	$scriptName = 'marvinette';
	if (!$isWindows) {
		$scriptPath = "/usr/local/bin";
	} else {
		$scriptPath = '.' . DIRECTORY_SEPARATOR;
		$scriptName .= '.ps1';
	}
	file_put_contents($scriptPath . $scriptName, getScriptContent($CWD, $isWindows));
	chmod($scriptPath . $scriptName, 0777);
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
	if ($isWindows) {
		echo "Add '" . getcwd() . "' to your PATH environment variable and you're ready to go!\n";
	}
} catch (Exception $e) {
	echo "An error occured: " . $e->getMessage() . "\n";
	echo "Exiting...\n";
	exit(1);
}
exit(0);
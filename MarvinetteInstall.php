<?php

$isWindows = sys_get_temp_dir() != '/tmp';

function copyFolder(string $sourcePath, string $destPath)
{
	if (!is_dir($destPath))
		mkdir($destPath);
	foreach (glob("$sourcePath/*") as $file) {
		if (is_dir($file)) {
			copyFolder($file, "$destPath/" . basename($file));
		} else {
			copy($file, "$destPath/" . basename($file));
		}
	}
}

function getInstallPath(): string
{
	global $isWindows;
	if ($isWindows)
		return getcwd() . DIRECTORY_SEPARATOR;
	$linuxInstallPath = $_SERVER['HOME'] . "/.local/lib/";
	if (is_dir($linuxInstallPath))
		return $linuxInstallPath;
	return getcwd() . DIRECTORY_SEPARATOR;
}

function install()
{
	global $isWindows;

	$scriptName = 'marvinette';
	if ($isWindows) {
		$installPath = getInstallPath();
		$scriptPath = "." . DIRECTORY_SEPARATOR;
		$scriptName .= '.ps1';
	} else {
		$installPath = getInstallPath();
		$scriptPath = $_SERVER['HOME'] . "/.local/bin/";
		if (!is_dir($scriptPath))
			$scriptPath = "/usr/bin/";
	} 
	file_put_contents($scriptPath . $scriptName, getScriptContent($installPath, $isWindows));
	chmod($scriptPath . $scriptName, 0777);
	if (!$isWindows) {
		$installPath = $_SERVER['HOME'] . "/.local/lib/";
		if (is_dir($scriptPath))
			copyFolder(".", $installPath . "marvinette");
	}
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

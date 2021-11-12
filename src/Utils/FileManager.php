<?php

class FileManager
{
	/**
	 * Deletes a folder, and its content
	 * @param string $folderPath the full or relative path to a folder
	 * @return void
	 */
	public static function deleteFolder(string $folderPath): void
	{
		foreach(glob("$folderPath/*") as $filePath) {
			if (is_dir($filePath)) {
				FileManager::deleteFolder($filePath);
			} else {
				unlink($filePath);
			}
		}
		rmdir($folderPath);
	}

	/**
	 * replace every backslashes and forwardslashes with DIRECTORY_SEPARATOR t oobtain cross-platform path
	 * @param string $path a file path containing directory separators
	 * @return string
	 */
	public static function normalizePath(string $path): string
	{
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
		return $path;
	}

	/**
	 * Remove last character if it is a directory separator
	 * @param string $ath a filepath
	 */
	public static function removeEndDirSeparator(string $path): string
	{
		while ((substr($path, -1, 1) === '/' || substr($path, -1, 1) === '\\') && strlen($path)) {
			$path = substr($path, 0, strlen($path) - 1);
		}
		return $path;
	}
}
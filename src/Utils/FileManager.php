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
            if (is_dir($filePath))
                FileManager::deleteFolder($filePath);
            else
                unlink($filePath);
        }
        rmdir($folderPath);
    }

    /**
     * replace every backslashes and forwardslashes with DIRECTORY_SEPARATOR
     * @param string $path a file path containing directory separators
     * @return string
     */
    public static function getCPPath(string $path): string
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}
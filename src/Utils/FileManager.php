<?php

class FileManager
{
    public static function deleteFolder(string $folderPath): void
    {
        foreach(glob("$folderPath/*") as $file) {
            $filePath = "$folderPath/$file";
            if (is_dir($filePath))
                FileManager::deleteFolder($filePath);
            else
                unlink($filePath);
        }
        rmdir($folderPath);
    }

    public static function getCPPath(string $path)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}
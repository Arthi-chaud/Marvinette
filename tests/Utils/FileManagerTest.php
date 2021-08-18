<?php

require_once 'tests/MarvinetteTestCase.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;

use function PHPUnit\Framework\assertEquals;

require_once 'src/Utils/FileManager.php';

final class FileManagerTest extends MarvinetteTestCase
{
	public function testDeleteFolder(): void
	{
		mkdir('tmp/my/dir', 0777, true);
		touch('tmp/my/file');
		touch('tmp/my/dir/file');
		$this->assertTrue(is_dir('tmp/my'));
		$this->assertTrue(is_dir('tmp/my/dir'));
		$this->assertTrue(file_exists('tmp/my/file'));
		$this->assertTrue(file_exists('tmp/my/dir/file'));

		FileManager::deleteFolder('tmp/my');
		$this->assertTrue(!is_dir('tmp/my'));
		$this->assertTrue(!is_dir('tmp/my/dir'));
		$this->assertTrue(!file_exists('tmp/my/file'));
		$this->assertTrue(!file_exists('tmp/my/dir/file'));
	}

	public function testGetCrossPlatformPath(): void
	{
		$path = "hello/world\\marvin";

		$this->assertEquals(FileManager::normalizePath($path), 'hello' . DIRECTORY_SEPARATOR . 'world' . DIRECTORY_SEPARATOR . 'marvin');
	}

	public function testRemoveLastDirSeparator(): void
	{
		$this->assertEquals(FileManager::removeEndDirSeparator('Hello/World/'), 'Hello/World');
		$this->assertEquals(FileManager::removeEndDirSeparator('Hello\\World\\'), 'Hello\\World');
	}
	public function testRemoveLastDirSeparatorMultiple(): void
	{
		$this->assertEquals(FileManager::removeEndDirSeparator('Hello/World///'), 'Hello/World');
		$this->assertEquals(FileManager::removeEndDirSeparator('Hello\\World\\\\\\'), 'Hello\\World');
	}

	public function testRemoveLastDirSeparatorFilledWith(): void
	{
		$this->assertEquals(FileManager::removeEndDirSeparator('///'), '');
		$this->assertEquals(FileManager::removeEndDirSeparator('\\\\\\\\'), '');
	}
}
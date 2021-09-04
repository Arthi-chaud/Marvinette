<?php

require_once 'src/Display/Color.php';
require_once 'src/Utils/UserInterface.php';
require_once 'src/Exception/MarvinetteException.php';

use Display\Displayer;
use Display\Color;

use function PHPUnit\Framework\throwException;

/**
 * @brief Everythong related to user interface
 */
class UserInterface
{
	public static Displayer $displayer;
	
	public static $titlesStack = []; 

	/**
	 * @brief Set title to display on every call of `displayCLIFrame`
	 * The title stack works like a stack, this is a push function
	 * @param string $title the title string
	 * @param bool $displayNow if true, will call displayTitle function
	 */
	public static function setTitle(string $title, bool $displayNow = false)
	{
		self::$titlesStack[] = $title;
		if ($displayNow) {
			self::displayTitle();
			echo "\n";
		}
	}

	/**
	 * Pops last set title from stack
	 * The title stack works like a stack, this is a pop function
	 */
	public static function popTitle(): void
	{
		array_pop(self::$titlesStack);
	}

	public static function displayTitle(): void
	{
		if (self::$titlesStack == []) {
			throw new MarvinetteException("No title set");
		}
		if (!isset($displayer)) {
			self::$displayer = new Displayer();
		}
		$text = end(self::$titlesStack);
		UserInterface::$displayer->setColor(Color::Green)
						->displayText("| $text\t|\t", false);
		reset(self::$titlesStack);
	}

	public static function displayHelp(): bool
	{
		$usage = [
			"marvinette [option]",
			"",
			"option:",
			"\t--create-project: Create a main configuration file, required to make tests",
			"\t--del-project: Delete configuration file and existing tests",
			"\t--mod-project: Modify the project's info.",
			"\t--add-test: Create a functionnal test",
			"\t--mod-test: Modify/Change an existing functionnal test",
			"\t--del-test: Delete a functionnal test",
			"\t-h, --help: display this usage"
		];
		foreach ($usage as $line) {
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor(Display\Color::White)->displayText($line);
		}
		return true;
	}

	/**
	 * Turns camelCase-formatted string into 'normally'-cased string
	 * @param string $str a camelCase string
	 * @return string the formatted string;
	 */
	public static function cleanCamelCase(string $str): string
	{
		$cleaned = "";
		for ($i = 0; $i < strlen($str); $i++) {
			$c = $str[$i];
			if ($i && ctype_upper($c)) {
				$cleaned .= " ";
			}
			$cleaned .= strtolower($c);
		}
		return $cleaned;
	}

	/**
	 * Turns a normally'-cased string into a camelCase-formatted string
	 * @param string $str a normally cased string
	 * @return string the camel case string;
	 */
	public static function toCamelCase(string $str): string
	{
		$cleaned = "";
		for ($i = 0; $i < strlen($str); $i++) {
			$c = $str[$i];
			if ($c == ' ') {
				continue;
			}
			if ($i && $str[$i - 1]  == ' ') {
				$cleaned .= strtoupper($c);
			} else {
				$cleaned .= strtolower($c);
			}
		}
		return $cleaned;
	}
}
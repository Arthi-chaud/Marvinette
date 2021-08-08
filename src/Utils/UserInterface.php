<?php

require_once 'src/Display/Color.php';
require_once 'src/Utils/UserInterface.php';

use Display\Displayer;
use Display\Color;

/**
 * @brief Everythong related to user interface
 */
class UserInterface
{
	public static Displayer $displayer;

	public static function displayCLIFrame(string $text): void
	{
		if (!isset($displayer))
			self::$displayer = new Displayer();
		UserInterface::$displayer->setColor(Color::Green)
						->displayText("| $text |\t", false);
		
	}

	public static function displayHelp(): bool
	{
		echo "marvinette [option]\n";
		echo "\toption:
		--create-project: Create a main configuration file, required to make tests
		--del-project: Delete configuration file and existing tests
		--mod-project: Modify the project's info.\n
		--add-test: Create a functionnal test
		--mod-test: Modify/Change an existing functionnal test\n
		--del-test: Delete a functionnal test
		-h, --help: display this usage\n";
		return true;
	}

	public static function cleanCamelCase(string $str): string
	{
		$cleaned = "";
		for ($i = 0; $i < strlen($str); $i++) {
			$c = $str[$i];
			if ($i && ctype_upper($c))
				$cleaned .= " ";
			$cleaned .= strtolower($c);
		}
		return $cleaned;
	}
}
<?php

require_once 'src/Utils/UserInterface.php';

class UserInput
{
	/**
	 * Reads a line from `stdin`. While what's entered is not in `$options`, `$questionPromts` is called and stdin is read
	 * @param callable $questionPrompt a function taking no parameter, called before each line read
	 * @param array $options an aray of string holding what is expected from stdin
	 * @return ?string null if stdin is closed or a string from $options read from the stream
	 */
	public static function getOption(callable $questionPrompt, array $options): ?string
	{
		$questionPrompt();
		while ($line = UserInput::getUserLine())
		{
			if (in_array($line, $options))
				return $line;
			$questionPrompt();
		}
		return null;
	}

	public static function getYesNoOption(string $displayFrameTitle, string $msg, $color): ?string
	{
		return self::getOption(function () use ($displayFrameTitle, $msg, $color)
		{
			UserInterface::displayCLIFrame($displayFrameTitle);
			UserInterface::$displayer->setColor($color)->displayText("$msg [Y/n]: ", false);
		}, ['Y', 'n']);
	}

	/**
	 * Reads a line from STDIN by calling `fgets`
	 * The line is trimmed
	 * Also used for unit testing
	 * @return string|false
	 */
	public static function getUserLine(): string
	{
		$line = false;
		if (!isset($GLOBALS['testSTDIN'])) {
			$line = fgets(STDIN);
		} else {
			$line = fgets($GLOBALS['testSTDIN']);
		}
		if ($line)
			$line = trim($line);
		return $line;
	}
}
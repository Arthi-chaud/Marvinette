<?php

require_once 'src/Utils/UserInterface.php';

class UserInput
{
	/**
	 * Reads a line from stdin. While what's entered is not in $options, $questionPromts is called and stdin is read
	 * The line read is trimmed
	 * @param callable $questionPrompt a function taking no parameter, called before each line read
	 * @param array $options an aray of string holding what is expected from stdin
	 * @return ?string null if stdin is closed or a string from $options read from the stream
	 */
	public static function getOption(callable $questionPrompt, array $options): ?string
	{
		$questionPrompt();
		while ($line = fgets(STDIN))
		{
			$line = trim($line);
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
}
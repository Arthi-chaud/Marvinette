<?php

require_once 'src/Utils/UserInterface.php';

class UserInput
{
	public static function getOption(callable $questionPrompt, $options): ?string
	{
		$questionPrompt();
		while ($line = fgets(STDIN))
		{
			$line = rtrim($line);
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
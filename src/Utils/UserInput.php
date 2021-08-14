<?php

require_once 'src/Exception/EndOfFileException.php';
require_once 'src/Utils/UserInterface.php';

class UserInput
{
	/**
	 * Reads a line from `stdin`. While what's entered is not in `$options`, `$questionPromts` is called and stdin is read
	 * @param callable $questionPrompt a function taking no parameter, called before each line read
	 * @param array $options an aray of string holding what is expected from stdin
	 * @return string a string from $options read from the stream, or throw if stream has nothing else to read
	 */
	public static function getOption(callable $questionPrompt, array $options): string
	{
		$questionPrompt();
		while ($line = UserInput::getUserLine())
		{
			if (in_array($line, $options))
				return $line;
			$questionPrompt();
		}
		throw new EndOfFileException();
	}

	/**
	 * Get Yes/no option from command-line interface
	 * @return bool true if user said yes, the opposite if no
	 */
	public static function getYesNoOption(string $msg, $color, ?string $displayFrameTitle = null): bool
	{
		$noChoices = ['n', 'N', 'no', 'non'];
		$yesChoices = ['y', 'Y', 'yes', 'Yes', 'oui'];
		if ($displayFrameTitle)
			UserInterface::setTitle($displayFrameTitle);
		$choice = self::getOption(function () use ($msg, $color)
		{
			UserInterface::displayTitle();
			UserInterface::$displayer->setColor($color)->displayText("$msg [Y/n]: ", false);
		}, array_merge($noChoices, $yesChoices));
		if ($displayFrameTitle)
			UserInterface::popTitle();
		return in_array($choice, $yesChoices);
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
		if (!$line)
			throw new EndOfFileException();
		$line = trim($line);
		return $line;
	}
}
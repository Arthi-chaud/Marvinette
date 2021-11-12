<?php

Class CommandLine
{
	/**
	 * Get command-line arguments 
	 * @param array $options array of valid options
	 */
	public static function getArguments($options): array
	{
		$shortopt = "";
		$longopts = [];
		foreach ($options as $option) {
			if (strlen($option) === 1) {
				$shortopt .= $option;
			} else {
				$longopts[] = $option;
			}
		}
		return getopt($shortopt, $longopts);
	}
}
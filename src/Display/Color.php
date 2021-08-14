<?php

namespace Display;

/**
 * @briefstatic class holding color int for terminal display
 */
class Color {
	/**
	 * @briefOffsert between text color and backgorund color (used for avoid creating another class)
	 */
	const BACKGROUND_OFFSET = 10;
	/**
	 * @briefid letting the terminal know to write using the default color
	 */
	const Default = 39;

	/**
	 * @briefid letting the terminal know to write using black
	 */

	const Black = 30;

	/**
	 * @briefid letting the terminal know to write using red
	 */
	const Red = 31;

	/**
	 * @briefid letting the terminal know to write using green
	 */
	const Green = 32;

	/**
	 * @briefid letting the terminal know to write using yellow
	 */
	const Yellow = 33;

	/**
	 * @briefid letting the terminal know to write using blue
	 */
	const Blue = 34;

	/**
	 * @briefid letting the terminal know to write using magenta
	 */
	const Magenta = 35;

	/**
	 * @briefid letting the terminal know to write using cyan
	 */
	const Cyan = 36;

	/**
	 * @briefid letting the terminal know to write using light gray
	 */
	const LightGray = 37;

	/**
	 * @briefid letting the terminal know to write using dark gray
	 */
	const DarkGray = 90;

	/**
	 * @briefid letting the terminal know to write using light red
	 */
	const LightRed = 91;

	/**
	 * @briefid letting the terminal know to write using light green
	 */
	const LightGreen = 92;

	/**
	 * @briefid letting the terminal know to write using light yellow
	 */
	const LightYellow = 93;

	/**
	 * @briefid letting the terminal know to write using light blue
	 */
	const LightBlue = 94;

	/**
	 * @briefid letting the terminal know to write using light magenta
	 */
	const LightMagenta = 95;

	/**
	 * @briefid letting the terminal know to write using light cyan
	 */
	const LightCyan = 96;

	/**
	 * @briefid letting the terminal know to write using white
	 */
	const White = 97;
}
?>

<?php

namespace Display;

/**
 *  static class holding color int for terminal display
 */
class Color {
	/**
	 *  Offsert between text color and backgorund color (used for avoid creating another class)
	 */
	const BACKGROUND_OFFSET = 10;
	/**
	 *  id letting the terminal know to write using the default color
	 */
	const Default = 39;

	/**
	 *  id letting the terminal know to write using black
	 */

	const Black = 30;

	/**
	 *  id letting the terminal know to write using red
	 */
	const Red = 31;

	/**
	 *  id letting the terminal know to write using green
	 */
	const Green = 32;

	/**
	 *  id letting the terminal know to write using yellow
	 */
	const Yellow = 33;

	/**
	 *  id letting the terminal know to write using blue
	 */
	const Blue = 34;

	/**
	 *  id letting the terminal know to write using magenta
	 */
	const Magenta = 35;

	/**
	 *  id letting the terminal know to write using cyan
	 */
	const Cyan = 36;

	/**
	 *  id letting the terminal know to write using light gray
	 */
	const LightGray = 37;

	/**
	 *  id letting the terminal know to write using dark gray
	 */
	const DarkGray = 90;

	/**
	 *  id letting the terminal know to write using light red
	 */
	const LightRed = 91;

	/**
	 *  id letting the terminal know to write using light green
	 */
	const LightGreen = 92;

	/**
	 *  id letting the terminal know to write using light yellow
	 */
	const LightYellow = 93;

	/**
	 *  id letting the terminal know to write using light blue
	 */
	const LightBlue = 94;

	/**
	 *  id letting the terminal know to write using light magenta
	 */
	const LightMagenta = 95;

	/**
	 *  id letting the terminal know to write using light cyan
	 */
	const LightCyan = 96;

	/**
	 *  id letting the terminal know to write using white
	 */
	const White = 97;
}
?>

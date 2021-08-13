<?php

namespace Display;

/**
 *  static class holding style int for terminal display
 */
class Style {
	/**
	 *  id letting the terminal know to write using default style
	 */
	const Default = 0;
	/**
	 *  id letting the terminal know to write in bold
	 */
	const Bold = 1;
	/**
	 *  id letting the terminal know to write in dim
	 */
	const Dim = 2;
	/**
	 *  id letting the terminal know to write underlined
	 */
	const Underlined = 4;
	/**
	 *  id letting the terminal know to write and blink
	 */
	const Blink = 5;
	/**
	 *  id letting the terminal know to write and invert foreground and background
	 */
	const ReverseForeBack = 7;
	/**
	 *  id letting the terminal know to write and hide
	 */
	const Hidden = 5;
}
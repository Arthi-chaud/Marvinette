<?php

namespace Display;

/**
 * @brief static class holding style int for terminal display
 */
class Style {
    /**
	 * @brief id letting the terminal know to write in bold
	 */
	const Bold = 1;
    /**
	 * @brief id letting the terminal know to write in dim
	 */
	const Dim = 2;
    /**
	 * @brief id letting the terminal know to write underlined
	 */
	const Underlined = 4;
    /**
	 * @brief id letting the terminal know to write and blink
	 */
	const Blink = 5;
    /**
	 * @brief id letting the terminal know to write and invert foreground and background
	 */
	const ReverseForeBack = 7;
    /**
	 * @brief id letting the terminal know to write and hide
	 */
	const Hidden = 5;
}
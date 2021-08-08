<?php

namespace Display;
require_once "src/Display/Color.php";
require_once "src/Display/Style.php";

/**
 * @brief Display utility class
 */
class Displayer
{
	/**
	 * @var int
	 * @brief Color to display the text with
	 * @warn must be an const from Display\Color
	 */
	protected $color = Color::Default;

	/**
	 * @var array
	 * @brief array of Styles to display the text with
	 * @warn must hold consts from Display\Style
	 */
	protected $styles = [];

	/**
	 * @var int
	 * @brief Background to display the text with
	 * @warn must be an const from Display\Color
	 */
	protected $background = Color::Default;
	
	/**
	 * Display text, using previously set style / color / background
	 * The style... will not be displayed if stdout is a TTY
	 * @param $text the text to display
	 * @param $resetAfter if true, resets style / color / background
	 */
	public function displayText(string $text, bool $newline = true, bool $resetAfter = true): self
	{
		$isTTY = stream_isatty(STDOUT);
		if ($isTTY) {
			echo $this->getSequence(Style::Default);
			echo $this->getSequence($this->color);
			echo $this->getSequence($this->background + Color::BACKGROUND_OFFSET);
			foreach ($this->styles as $style)
				echo $this->getSequence("\e[%dm", $style);
		}
		echo $text;
		if ($isTTY) {
			echo $this->getSequence(Style::Default);
			echo $this->getSequence(Color::Default);
			echo $this->getSequence(Color::Default + Color::BACKGROUND_OFFSET);
		}
		if ($resetAfter)
			$this->resetAll();
		if ($newline)
			echo "\n";
		return $this;
	}
	
	/**
	 * Get the value of color
	 */ 
	public function getColor()
	{
		return $this->color;
	}
	
	/**
	 * Set the value of color
	 *
	 * @return  self
	 */ 
	public function setColor($color)
	{
		$this->color = $color;
		
		return $this;
	}
	
	/**
	 * Set color filed to null
	 * 
	 * @return self
	 */
	public function resetColor(): self
	{
		$this->color = Color::Default;
		
		return $this;
	}
	/**
	 * Get the value of background
	 */ 
	public function getBackground()
	{
		return $this->background;
	}
	/**
	 * Set the value of background
	 *
	 * @return  self
	 */ 
	public function setBackground($background)
	{
		$this->background = $background;
		
		return $this;
	}
	
	/**
	 * Reset Background color value to default
	 */
	public function resetBackground(): self
	{
		$this->background = Color::Default;

		return $this;
	}

	/**
	 * Get the value of style
	 */ 
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Set the value of style
	 *
	 * @return  self
	 */ 
	public function setStyle($style)
	{
		$this->styles[] = $style;

		return $this;
	}

	/**
	 * Set styles
	 *
	 * @return  self
	 */ 
	public function setStyles($styles)
	{
		$this->styles = $styles;

		return $this;
	}

	/**
	 * Reset Styles value to none
	 */
	public function resetStyles(): self
	{
		$this->styles = [];

		return $this;
	}

	/**
	 * Set styles and color to use
	 * @param $styles an array of styles id
	 * @param $color a color id
	 * @param $background a background id (without offset, using straight color id)
	 */
	public function set(array $styles, $color, $background): self
	{
		$this->styles = array_merge($this->styles, $styles);
		$this->setColor($color);
		$this->setBackground($background);
		return $this;
	}
	
	/**
	 * resets all presets values, using rest memeber functions
	 */
	public function resetAll(): self
	{
		$this->resetBackground()->resetColor()->resetStyles();
		
		return $this;
	}

	/**
	 * @return string formatted string for terminal setting
	 */
	protected function getSequence($id): string
	{
		return sprintf("\e[%dm", $id);
	}
}
?>
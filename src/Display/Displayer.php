<?php

namespace Display;
include "Display\Color";
include "Display\Style";

/**
 * @brief Display utlity class
 */

class Displayer
{
    /**
     * @brief Color to display the text with
     * @warn must be an const from Display\Color
     */
    protected int $color = Color::Default;

    /**
     * @brief array of Styles to display the text with
     * @warn must hold consts from Display\Style
     */
    protected array $styles = [];

    /**
     * @brief Background to display the text with
     * @warn must be an const from Display\Background
     */
    protected ?int $background = Color::Default + Color::BACKGROUND_OFFSET;
    
    public function displayText(string $text, bool $resetAfter = true): self
    {
        if (!stream_isatty(STDOUT)) {
            echo $this->getSequence($this->color);
            echo $this->getSequence($this->background);
            foreach ($this->styles as $style)
                echo $this->getSequence("\e[%dm", $style);
        }
        echo $text;
        if ($resetAfter)
            $this->resetAll();
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
        $this->background = $background + Color::BACKGROUND_OFFSET;
        
        return $this;
    }
    
    public function resetBackground(): self
    {
        $this->background = null;

        return $this;
    }

    /**
     * Get the value of style
     */ 
    public function getStyles()
    {
        return $this->style;
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

    public function resetStyle(): self
    {
        $this->styles = [];

        return $this;
    }

    public function set(array $styles, $color, $background): self
    {
        $this->styles = array_merge($this->styles, $styles);
        $this->setColor($color);
        $this->setBackground($background);
        return $this;
    }
    
    public function resetAll(): self
    {
        $this->resetBackground()->resetColor()->resetStyle();
        
        return $this;
    }

    protected function getSequence($id): string
    {
        return sprintf("\e[%dm", $id);
    }
}
?>
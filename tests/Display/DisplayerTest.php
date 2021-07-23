<?php

require_once "src/Display/Displayer.php";
use PHPUnit\Framework\TestCase;
use Display\Displayer;
use Display\Color;
use Display\Style;

final class DisplayerTest extends TestCase
{
    public function testColorSettersGettersAndReset(): void
    {
        $displayer = new Displayer();

        $this->assertEquals($displayer->getColor(), Color::Default);
        $displayer->setColor(Color::DarkGray);
        $this->assertEquals($displayer->getColor(), Color::DarkGray);
        $displayer->resetColor();
        $this->assertEquals($displayer->getColor(), Color::Default);
    }

    public function testBackgroundSettersGettersAndReset(): void
    {
        $displayer = new Displayer();

        $this->assertEquals($displayer->getBackground(), Color::Default);
        $displayer->setBackground(Color::Green);
        $this->assertEquals($displayer->getBackground(), Color::Green);
        $displayer->resetBackground();
        $this->assertEquals($displayer->getBackground(), Color::Default);
    }

    public function testStylesSettersGettersAndReset(): void
    {
        $displayer = new Displayer();

        $this->assertEquals($displayer->getStyles(), []);
        $displayer->setStyle(Style::Bold);
        $this->assertEquals($displayer->getStyles(), [Style::Bold]);
        $displayer->setStyle(Style::Blink);
        $this->assertEquals($displayer->getStyles(), [Style::Bold, Style::Blink]);
        $displayer->setStyles([Style::Underlined]);
        $this->assertEquals($displayer->getStyles(), [Style::Underlined]);
        $displayer->resetStyles();
        $this->assertEmpty($displayer->getStyles());
    }
}

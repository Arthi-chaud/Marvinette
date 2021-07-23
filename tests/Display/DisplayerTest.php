<?php

require_once "src/Display/Displayer.php";
use PHPUnit\Framework\TestCase;
use Display\Displayer;
use Display\Color;

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
}

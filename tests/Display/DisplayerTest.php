<?php

require_once 'tests/MarvinetteTestCase.php';
require_once "src/Display/Displayer.php";
use PHPUnit\Framework\TestCase;
use Display\Displayer;
use Display\Color;
use Display\Style;

final class DisplayerTest extends MarvinetteTestCase
{
	public function testColorSettersGettersAndReset(): void
	{
		$displayer = new Displayer();

		$this->assertEquals(Color::Default, $displayer->getColor());
		$displayer->setColor(Color::DarkGray);
		$this->assertEquals(Color::DarkGray, $displayer->getColor());
		$displayer->resetColor();
		$this->assertEquals(Color::Default, $displayer->getColor());
	}

	public function testBackgroundSettersGettersAndReset(): void
	{
		$displayer = new Displayer();

		$this->assertEquals(Color::Default, $displayer->getBackground());
		$displayer->setBackground(Color::Green);
		$this->assertEquals(Color::Green, $displayer->getBackground());
		$displayer->resetBackground();
		$this->assertEquals(Color::Default, $displayer->getBackground());
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

	public function testSetterAndResetter(): void
	{
		$displayer = new Displayer();

		$displayer->set([Style::Bold], Color::Blue, Color::Black);
		$this->assertEquals($displayer->getStyles(), [Style::Bold]);
		$this->assertEquals($displayer->getColor(), Color::Blue);
		$this->assertEquals($displayer->getBackground(), Color::Black);
		$displayer->resetAll();
		$this->assertEmpty($displayer->getStyles());
		$this->assertEquals($displayer->getColor(), Color::Default);
		$this->assertEquals($displayer->getBackground(), Color::Default);
	}

	public function testDisplayTextOnTerminal(): void
	{
		$displayer = new Displayer();
		
		$this->expectOutputString("Hello World\nHello World");
		$displayer->setStyle(Style::Blink)
				  ->setColor(Color::Red)
				  ->displayText("Hello World");
		$displayer->displayText("Hello World", false, false);
	}

	public function testDisplayTextOnTty(): void
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		  );
		$displayer = new Displayer();
		$this->expectOutputString("Hello World\nHello World");
		$displayer->setStyle(Style::Blink)
		->setColor(Color::Red)
		->displayText("Hello World");
		$displayer->displayText("Hello World", false, false);
	}

	public function testResetAfterOption(): void
	{
		$this->hideStdout();
		$displayer = new Displayer();

		$displayer->setStyle(Style::Blink)
				  ->setColor(Color::Red)
				  ->displayText("Hello World");
		$this->assertEquals($displayer->getColor(), Color::Default);
		$this->assertEquals($displayer->getStyles(), []);
		$displayer->setStyle(Style::Blink)
				  ->setColor(Color::Red)
				  ->displayText("Hello World", true, false);
		$this->assertEquals($displayer->getColor(), Color::Red);
		$this->assertEquals($displayer->getStyles(), [Style::Blink]);
	}

	public function testGetSequence(): void
	{
		$displayer = new Displayer();
		$this->assertEquals("\e[1m", $this->callMethod($displayer, 'getSequence', [1]));
	}
}

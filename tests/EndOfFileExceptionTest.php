<?php

require_once 'src/Exception/EndOfFileException.php';
use PHPUnit\Framework\TestCase;

class EndOfFileExceptionTest extends TestCase
{
    public function testCatchEofException(): void
    {
        $catched = false;
        try {
            throw new EndOfFileException();
        } catch (EndOfFileException $e) {
            $catched = true;
        }
        $this->assertTrue($catched);
    }

    public function testCatchEofExceptionWhenMarvinetteExpected(): void
    {
        $catched = false;
        try {
            throw new EndOfFileException();
        } catch (MarvinetteException $e) {
            $catched = true;
        }
        $this->assertTrue($catched);
    }

    public function testCatchEofExceptionWhenExceptionExpected(): void
    {
        $catched = false;
        try {
            throw new EndOfFileException();
        } catch (Exception $e) {
            $catched = true;
        }
        $this->assertTrue($catched);
    }
}
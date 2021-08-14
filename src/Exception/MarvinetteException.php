<?php

/**
 * Base class for exceptions
 */
class MarvinetteException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
<?php

require_once 'src/Exception/MarvinetteException.php';
/**
 * Exception class for EOF error
 */
class EndOfFileException extends MarvinetteException {
    
    public function __construct()
    {
        parent::__construct("Nothing else to read from stream");
    }
}
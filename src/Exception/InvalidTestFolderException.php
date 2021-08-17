<?php

require_once 'src/Exception/MarvinetteException.php';
/**
 * Exception class for test folder errors
 */
class InvalidTestFolderException extends MarvinetteException {
	
	public function __construct()
	{
		parent::__construct("The tests folder is either non existant of contains no test");
	}
}
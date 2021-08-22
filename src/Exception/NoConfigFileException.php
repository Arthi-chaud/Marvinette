<?php

require_once 'src/Exception/MarvinetteException.php';
/**
 * Exception class for test folder errors
 */
class NoConfigFileException extends MarvinetteException {
	
	public function __construct()
	{
		parent::__construct("No configuration file found. Use `marvinette --create-project`");
	}
}
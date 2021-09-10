<?php

require_once 'src/Exception/MarvinetteException.php';
/**
 * Exception class for test folder errors
 */
class InvalidConfigFileException extends MarvinetteException {
	
	public function __construct()
	{
		parent::__construct("Invalid Marvinette.json. Use `--create-project` or `--create-sample-project`");
	}
}
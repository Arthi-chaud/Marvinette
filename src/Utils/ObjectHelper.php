<?php 

require_once 'src/Display/Color.php';

use Display\Color;

/**
 * Set of function which help iterate through object's fields
 */
class ObjectHelper
{

	/**
	 * Calls $callable on each field of $obj
	 * @param callable $callable a function taking 3 parameters: a reference to the parent object, the data name and the data. retusn true on success, false on error, null on fatal error
	 * @param object $obj a refence to an object
	 * @return bool true if everything succeeded
	 */
	public static function forEachObjectField(&$obj, callable $callable): void
	{
		foreach (get_object_vars($obj) as $fieldName => $field)
			for ($choosen = false; !$choosen; ) {
				$choosen = $callable($fieldName, $field);
			}
	}

	/**
	 * Prompt user to enter each obbjec's field
	 * @param object $obj the object to iterate through
	 * @param callable $promptFormatter a function that display prompt using field's name and field
	 * @param bool $modPrompt if true and value entered is empty, the old value is not modified 
	 * @param array $ignoredFields an array of string containing fields' names that will be ingore at prompt
	 */
	public static function promptEachObjectField(&$obj, callable $displayPrompt,  bool $modPrompt = false, array $ignoredFields = [])
	{
		self::forEachObjectField($obj, function($fieldName, $field) use ($displayPrompt, $modPrompt, $ignoredFields) {
			if (in_array($fieldName, $ignoredFields))
				return true;
			$displayPrompt($fieldName, $field);
			$value = UserInput::getUserLine();
			if ($modPrompt && $value == "")
				return true;
			try {
				$field->set($value);
				return true;
			} catch (Exception $e) {
				UserInterface::displayTitle();
				UserInterface::$displayer->setColor(Color::Red)->displayText($e->getMessage());
				return false;
			}
		});
	}
}
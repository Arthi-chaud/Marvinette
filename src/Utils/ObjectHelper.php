<?php 

class ObjectHelper
{

	/**
	 * Calls $callable on each field of $obj
	 * @param callable $callable a function taking 3 parameters: a reference to the parent object, the data name and the data. retusn true on success, false on error, null on fatal error
	 * @param object $obj a refence to an object
	 * @return bool true if everything succeeded
	 */
	public static function forEachObjectField(&$obj, callable $callable): bool
	{
		foreach (get_object_vars($obj) as $fieldName => $field)
			for ($choosen = false; !$choosen; ) {
				$choosen = $callable($fieldName, $field);
				if ($choosen == null)
					return false;
			}
		return true;
	}
}
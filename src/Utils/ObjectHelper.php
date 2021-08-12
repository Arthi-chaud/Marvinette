<?php 

class ObjectHelper
{

	public static function forEachObjectField(&$obj, callable $callable): bool
	{
		foreach (get_object_vars($obj) as $fieldName => $field)
			for ($choosen = false; !$choosen; ) {
				$choosen = $callable($obj, $fieldName, $field);
				if ($choosen == null)
					return false;
			}
		return true;
	}
}
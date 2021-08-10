<?php

/**
 * @brief Object representing a class's variable member, but allows error handling and prompt help messages
*/
class Field
{
	public static function YesNoErrorHandler($choice)
	{
		if (!in_array($choice, ['Y', 'N', '', 'y', 'n']))
			throw new Exception("Please type 'Y', 'N' or leave empty");
	}

	public static function YesNoDataCleaner($choice)
	{
		if (in_array($choice, ['Y', 'y']))
			return true;
		return false;
	}

	public static function EmptyDataCleaner($input)
	{
		if ($input == "")
			return null;
		return $input;
	}

	/**
	 * @brief A Field constructor, should be called in constructor of class
	 * @param callable $errorHandler the function to handle error: must throw when error occurs (will be called using call_user_func)
	 * @param callable $datacleaner the function to clean the data (can be null) (will be called using call_user_func)
	 * @param string $promptHelp a help message, which will be displayed when the user is prompted to type the field
	 */
	public function __construct(callable $errorHandler, $dataCleaner = null, ?string $promptHelp = null)
	{
		$this->errorHandler = $errorHandler;
		$this->dataCleaner = $dataCleaner;
		$this->promptHelp = $promptHelp;
	}

	/**
	 * @brief the data in itself, by default is null
	 * @var mixed
	 */
	protected $data = null;

	/**
	 * @brief the function to handle errrors
	 * @info throw on error or return void
	 * @var callable
	 */
	protected $errorHandler;

	/**
	 * @brief the function to clean data
	 * @info return the cleaned data
	 * @var callable
	 */
	protected $dataCleaner;

	/**
	 * @brief an array of string to display on prompt to help the user
	 * @var array
	 */
	protected $promptHelp = [];

	/**
	 * Get the value of data
	 *
	 * @return  mixed
	 */ 
	public function get()
	{
		return $this->data;
	}

	/**
	 * @brief call setter
	 */
	public function set($data): void
	{
		call_user_func($this->errorHandler, $data);
		if ($this->dataCleaner)
			$data = call_user_func($this->dataCleaner, $data);
		$this->data = $data;
	}

	/**
	 * Get prompt helper
	 *
	 * @return  array
	 */ 
	public function getPromptHelp()
	{
		return $this->promptHelp;
	}
}
<?php

require_once 'src/Exception/MarvinetteException.php';

/**
 * @brief Object representing a class's variable member, but allows error handling and prompt help messages
*/
class Field
{
	/**
	 * Converts data to string when possible
	 * Avoid using `get` getter method
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->data;
	}

	/**
	 * Error handler for object's field
	 * Throws an exception if $choice is not a valid 'yes'/'no' string
	 * @param string $choice a string entered by the user
	 * @return void
	 */
	public static function YesNoErrorHandler($choice): void
	{
		if (!in_array($choice, ['Y', 'N', '', 'y', 'n', 'yes', 'no', 'oui', 'non']))
			throw new MarvinetteException("Please type 'Y', 'N' or leave empty");
	}

	/**
	 * Data cleaner function
	 * If $choice is a 'yes' option string, the function returns true
	 * @param string $choice a string entered by the user
	 * @return bool
	 */
	public static function YesNoDataCleaner($choice): bool
	{
		if (in_array($choice, ['Y', 'y', 'yes', 'oui']))
			return true;
		return false;
	}

	/**
	 * Fata cleaner function
	 * If the user entered an empty line, the data will be set to null
	 * @param string $input a string entered by the user
	 * @return string|null
	 */
	public static function EmptyDataCleaner($input): ?string
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
	 * @brief a string to display on prompt to help the user
	 * @var ?string
	 */
	protected ?string $promptHelp = null;

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
	 * @brief Call the error handler, with no exception handling
	 * If a data cleaner is set, the value retuend by this function will be set to `$data`
	 * `$data` is set to the object's data field
	 * @param mixed $data a value entered by the user
	 * @return void
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
	 * @return ?string
	 */ 
	public function getPromptHelp(): ?string
	{
		return $this->promptHelp;
	}
}
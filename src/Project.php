<?php

/**
 * @brief Object representing the project's important infos
*/
class Project
{
	/**
	 * @brief The name of the project
	*/
	protected string $name;

	/**
	 * @brief Name of the binary to execute
	 * @details not a path, just the binary name
	*/
	protected string $binaryName;

	/**
	 * @brief Path to the binary to execute
	 * @details Can be either realtive or absolute
	 */
	protected string $binaryPath;

	/**
	 * @brief name of the interpreter, if needed
	 */
	protected ?string $interpreter;

	/**
	 * @brief Relative path to the folder holding tests files
	 */
	protected string $testsFolder;

	/**
	 * Set the value of name
	 *
	 * @return  self
	 */ 
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the name of the project
	 *
	 * @return  string
	 */ 
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the value of binaryName
	 */ 
	public function getBinaryName()
	{
		return $this->binaryName;
	}

	/**
	 * Set the value of binaryName
	 *
	 * @return  self
	 */ 
	public function setBinaryName($binaryName)
	{
		$this->binaryName = $binaryName;

		return $this;
	}

	/**
	 * Get the value of binaryPath
	 */ 
	public function getBinaryPath()
	{
		return $this->binaryPath;
	}

	/**
	 * Set the value of binaryPath
	 *
	 * @return  self
	 */ 
	public function setBinaryPath($binaryPath)
	{
		$this->binaryPath = $binaryPath;

		return $this;
	}

	/**
	 * Get the value of interpreter
	 */ 
	public function getInterpreter()
	{
		return $this->interpreter;
	}

	/**
	 * Set the value of interpreter
	 *
	 * @return  self
	 */ 
	public function setInterpreter($interpreter)
	{
		$this->interpreter = $interpreter;

		return $this;
	}

	/**
	 * Get the value of testsFolder
	 */ 
	public function getTestsFolder()
	{
		return $this->testsFolder;
	}

	/**
	 * Set the value of testsFolder
	 *
	 * @return  self
	 */ 
	public function setTestsFolder($testsFolder)
	{
		$this->testsFolder = $testsFolder;

		return $this;
	}
}

?>
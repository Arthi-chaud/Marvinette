<?php

use phpDocumentor\Reflection\Types\Boolean;

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
	 * @brief Path to the binary to execute, by default cwd
	 * @details Can be either realtive or absolute
	 */
	protected string $binaryPath = "./";

	/**
	 * @brief name of the interpreter, if needed
	 */
	protected ?string $interpreter = null;

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
		if (strchr($binaryName, DIRECTORY_SEPARATOR))
			throw new Exception("The binary name should not contain a '". DIRECTORY_SEPARATOR. "'");
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
		if ($binaryPath == "")
			$binaryPath = ".";
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
		if ($interpreter == "")
			$interpreter = null;
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

	/**
	 * Returns true if all necessary fields are set
	 * interpreter can be null
	 */
	public function readyToExport(): bool
	{
		return $this->name && $this->binaryPath && $this->binaryName && $this->testsFolder;
	}

	/**
	 * build binary access path
	 */
	protected function buildBinaryAccessPath(): string
	{
		if (substr($this->binaryPath, strlen($this->binaryPath) - 1, 1) != DIRECTORY_SEPARATOR)
			$this->binaryPath .= DIRECTORY_SEPARATOR;
		return $this->binaryPath . $this->binaryName;
	}

	/**
	 * Using PATh Env var, checks if interpreter exists
	 * throw if no interpreter is set
	 */
	protected function interpreterExists(): bool
	{
		if (!$this->interpreter)
			throw new Exception("No Interpreter set");
		foreach (explode(':', getenv('PATH')) as $path) {
			if (file_exists($path . DIRECTORY_SEPARATOR . $this->interpreter))
				return true;
		}
		return false;
	}

	/**
	 * Checks if the binary exists the interpreter exists (if set) (need to be ready to export)
	 */
	public function isReadyToBeTested(): bool
	{
		if (!$this->readyToExport())
			return false;
		if (!file_exists($this->buildBinaryAccessPath()))
			return false;
		if ($this->interpreter && !$this->interpreterExists())
			return false;
		return true;
	}

	/**
	 * Export Project to JSON formatted file
	 * @param $outfile name of file with JSON-ed Project class
	 */
	public function export(string $outfile): bool
	{
		$project['name'] = $this->name;
		$project['binary name'] = $this->binaryName;
		$project['binary path'] = $this->binaryPath;
		$project['interpreter'] = $this->interpreter;
		$project['tests folder'] = $this->testsFolder;

		if (!$this->readyToExport())
			throw new Exception("Project is not ready to be exported, missing mandatory field");
		$jsoned = json_encode($project, JSON_PRETTY_PRINT);
		//to do: if file already exists, prompt to overwite
		return file_put_contents($outfile, $jsoned);
	}

	public function import(string $infile): void
	{
		$expectedKeys = [
			'name',
			'binary name',
			'binary path',
			'interpreter',
			'tests folder',	
		];
		if (!file_exists($infile))
			throw new Exception("$infile does not exists.");
		$object = json_decode(file_get_contents($infile), true);
		if (!$object)
			throw new Exception("File $infile: Invalid JSON File.");
		foreach ($expectedKeys as $expectedKey) {
			if (!array_key_exists($expectedKey, $object))
				throw new Exception("File $infile: No '$expectedKey' field.");
		}
		$this->setName($object['name'])
			 ->setBinaryPath($object['binary path'])
			 ->setBinaryName($object['binary name'])
			 ->setInterpreter($object['interpreter'])
			 ->setTestsFolder($object['tests folder']);
		
	}
}

?>
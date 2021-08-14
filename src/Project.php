<?php

require_once 'src/Field.php';

/**
 * @briefObject representing the project's important infos
*/
class Project
{

	const ConfigurationFile = "Marvinette.json";

	public function __construct()
	{
		$this->name = new Field(function($name) {
			if (!$name)
				throw new Exception("The Project's name shouldn't be empty");
			return $name;
		});

		$this->binaryName = new Field(function($binaryName) {
			if (!$binaryName)
				throw new Exception("The Project's binary name shouldn't be empty");
			if (strchr($binaryName, DIRECTORY_SEPARATOR))
				throw new Exception("The binary name should not contain a '". DIRECTORY_SEPARATOR. "'");
		});

		$this->binaryPath = new Field(function($binaryPath) {}, function($binaryPath) {
			if ($binaryPath == "")
				$binaryPath = ".";
			return FileManager::removeEndDirSeparator($binaryPath);
		}, "By default: Current directory");
		$this->binaryPath->set(".");
		
		$this->interpreter = new Field(function($interpreter) {}, [Field::class, 'EmptyDataCleaner'], "By default: none (when it is an ELF file or a script using a shebang)");

		$this->testsFolder = new Field(function($testFolder) {}, function($testFolder) {
			if ($testFolder == "")
				$testFolder = "tests";
			return FileManager::removeEndDirSeparator($testFolder);
		}, "By default in 'tests' folder");
		$this->testsFolder->set("tests");
	}

	/**
	 * @briefThe name of the project
	 * @var string
	*/
	public Field $name;	

	/**
	 * @briefName of the binary to execute
	 * @details not a path, just the binary name
	 * @var string
	*/
	public Field $binaryName;

	/**
	 * @briefPath to the binary to execute, by default cwd
	 * @details Can be either realtive or absolute
	 * @var string
	 */
	public Field $binaryPath;

	/**
	 * @briefname of the interpreter, if needed
	 * @var string
	 */
	public Field $interpreter;

	/**
	 * @briefRelative path to the folder holding tests files
	 * @var string
	 */
	public Field $testsFolder;


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
			if (file_exists(FileManager::getCPPath("$path/". $this->interpreter->get())))
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
		$project['name'] = $this->name->get();
		$project['binary name'] = $this->binaryName->get();
		$project['binary path'] = $this->binaryPath->get();
		$project['interpreter'] = $this->interpreter->get();
		$project['tests folder'] = $this->testsFolder->get();

		if (!$this->readyToExport())
			throw new Exception("Project is not ready to be exported, missing mandatory field");
		$jsoned = json_encode($project, JSON_PRETTY_PRINT);
		return file_put_contents($outfile, $jsoned);
	}

	public function import(string $infile): void
	{
		if (!file_exists($infile))
			throw new Exception("$infile does not exists.");
		$object = json_decode(file_get_contents($infile), true);
		if (!$object)
			throw new Exception("File $infile: Invalid JSON File.");
		foreach($object as $fieldName => $field) {
			$cameCalseName = UserInterface::toCamelCase($fieldName);
			$this->$cameCalseName->set($field);
		}
	}
}

?>
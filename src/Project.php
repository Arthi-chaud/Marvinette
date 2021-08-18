<?php

require_once 'src/Field.php';

/**
 * @brief Object representing the project's important infos
*/
class Project
{

	const ConfigurationFile = "Marvinette.json";

	/**
	 * @param ?string $filePath a path to a JSON Project file
	 */
	public function __construct(?string $filePath = null)
	{
		$this->name = new Field(function($name) {
			if (!$name)
				throw new Exception("The Project's name shouldn't be empty");
			return $name;
		});

		$this->binaryName = new Field(function($binaryName) {
			if (!$binaryName)
				throw new Exception("The Project's binary name shouldn't be empty");
			if (strchr($binaryName, '/') || strchr($binaryName, '\\'))
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

		if ($filePath)
			$this->import($filePath);
	}

	/**
	 * @brief The name of the project
	 * @var string
	*/
	public Field $name;	

	/**
	 * @brief Name of the binary to execute
	 * @details not a path, just the binary name
	 * @var string
	*/
	public Field $binaryName;

	/**
	 * @brief Path to the binary to execute, by default cwd
	 * @details Can be either realtive or absolute
	 * @var string
	 */
	public Field $binaryPath;

	/**
	 * @brief name of the interpreter, if needed
	 * @var string
	 */
	public Field $interpreter;

	/**
	 * @brief Relative path to the folder holding tests files
	 * @var string
	 */
	public Field $testsFolder;


	/**
	 * @return bool true if all necessary fields are set
	 * interpreter can be null
	 */
	public function readyToExport(): bool
	{
		return $this->name->get() && $this->binaryPath->get() && $this->binaryName->get() && $this->testsFolder->get();
	}

	/**
	 * @return string binary access path
	 */
	public function buildBinaryAccessPath(): string
	{
		return $this->binaryPath->get() . DIRECTORY_SEPARATOR . $this->binaryName->get();
	}

	/**
	 * Using PATh Env var, checks if interpreter exists
	 * throw if no interpreter is set
	 */
	public function interpreterExists(): bool
	{
		if (!$this->interpreter->get())
			throw new Exception("No Interpreter set");
		foreach (explode(':', getenv('PATH')) as $path) {
			if (file_exists(FileManager::normalizePath("$path/". $this->interpreter->get())))
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
		if ($this->interpreter->get() && !$this->interpreterExists())
			return false;
		return true;
	}

	/**
	 * Export Project to JSON formatted file
	 * @param $outfile name of file with JSON-ed Project class
	 */
	public function export(string $outfile): void
	{
		$project = [];
		if (!$this->readyToExport())
			throw new Exception("Project is not ready to be exported, missing mandatory field");
		foreach(get_object_vars($this) as $fieldName => $field)
			$project[UserInterface::cleanCamelCase($fieldName)] = $field->get();
		$jsoned = json_encode($project, JSON_PRETTY_PRINT);
		file_put_contents($outfile, $jsoned);
	}

	/**
	 * Fills the object's field using json file
	 * @param string $infile the path to a valid JSON Project file
	 */
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
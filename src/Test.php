<?php

use PHPUnit\TextUI\XmlConfiguration\File;

use function PHPUnit\Framework\throwException;

require_once "src/Field.php";

/**
 * @brief Object representing the test's instruction
*/
class Test
{
	const TMPFOLDER = 'tmp';

	const setupFileName = 'setup';

	const teardownFileName = 'teardown';

	const stdoutFilterFileName = 'stdoutFilter';

	const stderrFilterFileName = 'stderrFilter';

	const expectedReturnCodeFile = 'expectedReturnCode';

	const stdinputFile = 'stdinput';

	const expectedStderrFile = 'expectedStderr';

	const expectedStdoutFile = 'expectedStdout';

	const commandFile = 'command';

	public function __construct()
	{
		$this->name = new Field(function($name) {
			if (!$name)
				throw new Exception("The test's name shouldn't be empty");
			if (strchr($name, DIRECTORY_SEPARATOR))
				throw new Exception("The test's name should not contain a '". DIRECTORY_SEPARATOR. "'");
		});

		$this->commandLineArguments = new Field(function($args) {}, null, "The arguments to pass to the program");

		$this->expectedReturnCode = new Field(
		function($r) {
			if ($r == "")
				return;
			if (!is_numeric($r) || (intval($r) < 0))
				throw new Exception("Please enter a number superior/equal to 0 (or nothing to ignore)");
		}, function($r) {
			if ($r == "")
				return null;
			return intval($r);
		}, "A number between 0 and 255, Leave empty to ignore");

		$this->stdoutFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "A command that will grep the stdout of the program");

		$this->stderrFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "A command that will grep the stderr of the program");

		$this->stdinput = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"[Y/n] By default no. If you say yes, an empty file will be created so you can set what to write on stdin");
		$this->expectedStdout = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"[Y/n] By default none. If you say yes, an empty file will be created so you can set what to expect on stdout");
		$this->expectedStderr = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"[Y/n] By default none. If you say yes, an empty file will be created so you can set what to expect on stderr");
		
		$this->setup = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "Command to execute before executing program");

		$this->teardown = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "Command to execute after program's execution");
	}

	public function export(?Project $project)
	{
		if (!$project) {
			$project = new Project();
			$project->import(Marvinette::ConfigurationFile);
		}
		$testsFolder = $project->testsFolder->get();
		$testPath = FileManager::getCPPath("$testsFolder/" . $this->name->get());
		if (!is_dir($testPath))
			mkdir($testPath, 0777, true);
		foreach(get_object_vars($this) as $fieldName => $field) {
			if ($fieldName == 'name')
				continue;
			if (is_bool($field->get()) && $field->get())
				file_put_contents(FileManager::getCPPath("$testPath/$fieldName"), '');
			else if (is_string($field->get()) && $field->get())
				file_put_contents(FileManager::getCPPath("$testPath/$fieldName"), $field->get());
		}
		return true;
	}

	/**
	 * @brief import test from file in folder
	 * @param $testsFolder the path to the test folder
	 */
	public function import(string $testFolder)
	{
		$testName = basename($testFolder);

		if (!is_dir($testFolder))
			throw new Exception('Invalid test path');
		$this->name->set($testName);
		foreach(get_object_vars($this) as $fieldName => $_)
			if (file_exists(FileManager::getCPPath("$testFolder/$fieldName"))) {
				$fileContent = file_get_contents(FileManager::getCPPath("$testFolder/$fieldName"));
				if (is_numeric($fileContent))
					$fileContent = intval($fileContent);
				$this->$fieldName->set(file_get_contents(FileManager::getCPPath("$testFolder/$fieldName")));
			}
	}

	public function execute(Project $project): bool
	{
		$testPath = $project->testsFolder->get() . DIRECTORY_SEPARATOR . $this->name->get();
		$expectedReturnCode = $this->expectedReturnCode->get();
		$interpreter = $project->interpreter->get();
		$actualReturnCode = 0;
		if ($this->setup->get()) {
			system($this->setup->get(), $actualReturnCode);
			if ($actualReturnCode != 0)
				throw new Exception("Test's setup failed. Return code: $actualReturnCode");
		}
		$command = $project->binaryPath->get() . DIRECTORY_SEPARATOR . $project->binaryName->get();
		if ($this->commandLineArguments->get())
			$command .= ' ' . $this->commandLineArguments->get();
		if ($interpreter != null)
			$command = "$interpreter $command";
		system($command . "> tmp/MarvinetteStdout 2> tmp/MarvinetteStderr", $actualReturnCode);
		if ($expectedReturnCode != null && $expectedReturnCode != $actualReturnCode)
			throw new Exception("The program didn't return the expected code. Expected: $actualReturnCode, actual: $expectedReturnCode");
		foreach(['stdout', 'stderr'] as $output) {
			$filter = $output . 'Filter';
			$ustream = ucwords($output);
			$expected = "expected$ustream";
			if ($this->$filter->get()) {
				$filterCommand = $this->$filter->get();
				system("cat tmp/Marvinette$ustream | $filterCommand > tmp/MarvinetteFiltered$ustream", $actualReturnCode);
				if ($actualReturnCode != 0)
					throw new Exception("Test's $output filtering failed. Return code: $actualReturnCode");
				system("cat tmp/MarvinetteFiltered$ustream > tmp/Marvinette$ustream");
			}
			if ($this->$expected->get()) {
				$expectedStdoutFile = FileManager::getCPPath("$testPath/expectedStdout");
				system(FileManager::getCPPath("diff $expectedStdoutFile tmp/Marvinette$ustream"), $actualReturnCode);
				if ($actualReturnCode != 0)
					return false;
			}
		}
		if ($this->teardown->get()) {
			system($this->teardown->get(), $actualReturnCode);
			if ($actualReturnCode != 0)
				throw new Exception("Test's teardown failed. Return code: $actualReturnCode");
		}
		return true;
	}

	/**
	 * @brief The name of the test
	 * @var string
	 */
	public Field $name;

	/**
	 * @brief The arguements to send to the programm
	 * @var ?string
	*/
	public Field $commandLineArguments;

	/**
	 * @brief The return code expected at the end of the test
	 * @var int
	*/
	public Field $expectedReturnCode;

	/**
	 * @brief command (and args) to execute piped to the std output of the program
	 * @var array
	 */
	public Field $stdoutFilter;

	/**
	 * @brief command (and args) to execute piped to the error output of the program
	 * @var array
	 */
	public Field $stderrFilter;

	/**
	 * @brief the test should read from stdin
	 * @var bool
	 */
	public Field $stdinput;

	/**
	 * @brief if true, will compare program's stdout to file
	 * @var bool
	 */
	public Field $expectedStdout;

	/**
	 * @brief if true, will compare program's stderr to file
	 * @var string
	 */
	public Field $expectedStderr;

	/**
	 * @brief command to execute before test
	 * @var string
	 */
	public Field $setup;

	/**
	 * @brief command to execute after test
	 * @var string
	 */
	public Field $teardown;
}
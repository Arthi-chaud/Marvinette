<?php

use function PHPUnit\Framework\throwException;

require_once "src/Field.php";

/**
 * @brief Object representing the test's instruction
*/
class Test
{
	public function __construct()
	{
		$this->name = new Field(function($name) {
			if (!$name)
				throw new Exception("The test's name shouldn't be empty");
			return $name;
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
		$testPath = "$testsFolder" . DIRECTORY_SEPARATOR . $this->name->get();
		if (!is_dir($testPath))
			mkdir($testPath, 0777, true);

		if ($this->setup->get() != null)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "setup", $this->setup->get());
		if ($this->teardown->get() != null)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "stderrFilter", $this->teardown->get());
		if ($this->stdoutFilter->get() != null)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "stdoutFilter", $this->stdoutFilter->get());
		if ($this->stderrFilter->get() != null)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "stderrFilter", $this->stderrFilter->get());
		if ($this->expectedReturnCode->get() != null)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "expectedReturnCode", $this->expectedReturnCode->get());
		if ($this->stdinput->get() == true)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "stdinput", '');
		if ($this->expectedStderr->get() == true)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "expectedStderr", '');
		if ($this->expectedStdout->get() == true)
			file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "expectedStdout", '');
		file_put_contents("$testPath" . DIRECTORY_SEPARATOR . "command",
			$project->binaryPath->get() . DIRECTORY_SEPARATOR . $project->binaryName->get() . " " . $this->commandLineArguments->get());
		return true;
	}
	/**
	 * @brief The name of the test
	 * @var string
	 */
	public Field $name;

	/**
	 * @brief The arguements to send to the programm
	 * @var array
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
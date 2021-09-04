<?php

use PhpParser\Node\Expr\Exit_;
use PHPUnit\TextUI\XmlConfiguration\File;
use Display\Color;
require_once 'src/Exception/MarvinetteException.php';
require_once 'src/Field.php';

define('TmpFileFolder', sys_get_temp_dir());

/**
 * @brief Object representing the test's instruction
*/
class Test
{
	const TmpFileFolder = TmpFileFolder;

	const TmpFilePrefix = 'Marvinette';

	const TmpFileStdoutPrefix = 'Stdout';

	const TmpFileStderrPrefix = 'Stderr';

	const TmpFileFilteredPrefix = 'Filtered';

	const TmpDiffFilePrefix = 'Diff';

	public function __construct(?string $testPath = null)
	{
		$this->name = new Field(function($name) {
			if (!$name) {
				throw new MarvinetteException("The test's name shouldn't be empty");
			}
			if (strchr($name, DIRECTORY_SEPARATOR)) {
				throw new MarvinetteException("The test's name should not contain a '". DIRECTORY_SEPARATOR. "'");
			}
		});

		$this->commandLineArguments = new Field(function($args) {}, null, "The arguments to pass to the program");

		$this->interpreterArguments = new Field(function($args) {}, null, "The arguments to pass to the interpreter");

		$this->expectedReturnCode = new Field(
		function($r) {
			if ($r == "") {
				return;
			}
			if (!is_numeric($r) || (intval($r) < 0) || (intval($r) >= 255)) {
				throw new MarvinetteException("Please enter a number superior/equal to 0 (or nothing to ignore)");
			}
		}, function($r) {
			if ($r == "") {
				return null;
			}
			return intval($r);
		}, "A number between 0 and 255, Leave empty to ignore");

		$this->stdoutFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "A command that will grep the stdout of the program");

		$this->stderrFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "A command that will grep the stderr of the program");

		$this->stdinput = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"[Y/n] By default no. If you say yes, an empty file will be created so you can set what to write on stdin");
		$this->emptyEnv = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"[Y/n] By default no. If you say yes, in this test, the program will be executed with an empty env");
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
		if ($testPath) {
			$this->import($testPath);
		}
	}

	/**
	 * Will export the test in a folder placed in $testsFolder`
	 * @param string $testsFolder the path to the folder where the test's folder will be placed
	 */
	public function export(string $testsFolder): void
	{
		$testPath = FileManager::normalizePath("$testsFolder/" . $this->name->get());
		if (is_dir($testPath)) {
			FileManager::deleteFolder($testPath);
		}
		mkdir($testPath, 0777, true);
		foreach(get_object_vars($this) as $fieldName => $field) {
			if ($fieldName == 'name') {
				continue;
			}
			if (is_bool($field->get()) && $field->get()) {
				file_put_contents(FileManager::normalizePath("$testPath/$fieldName"), '');
			} else if (is_string($field->get()) && $field->get()) {
				file_put_contents(FileManager::normalizePath("$testPath/$fieldName"), $field->get());
			} else if (is_numeric($field->get())) {
				file_put_contents(FileManager::normalizePath("$testPath/$fieldName"), $field->get());
			}
		}
	}

	/**
	 * @brief import test from file in folder
	 * @param $testsFolder the path to the test folder
	 */
	public function import(string $testFolder)
	{
		$testName = basename($testFolder);

		if (!is_dir($testFolder)) {
			throw new MarvinetteException('Invalid test path');
		}
		$this->name->set($testName);
		foreach(get_object_vars($this) as $fieldName => $field) {
			if (!file_exists(FileManager::normalizePath("$testFolder/$fieldName"))) {
				continue;
			}
			$fileContent = file_get_contents(FileManager::normalizePath("$testFolder/$fieldName"));
			if (is_numeric($fileContent)) {
				$fileContent = intval($fileContent);
			}
			if ($fileContent == '' && is_string($fileContent)) {
				$this->$fieldName->set(true);
				continue;
			}
			try {
				$this->$fieldName->set($fileContent);
			} catch (Exception $_) {
				$this->$fieldName->set(true);
			}
		}
	}

	/**
	 * Executes A Test, Throws when ann error occurs or on fail
	 * @return bool true on success
	 */
	public function execute(Project $project): void
	{
		$testPath = $project->testsFolder->get() . DIRECTORY_SEPARATOR . $this->name->get();
		if ($this->setup->get()) {
			$this->executeSystemCommand($this->setup->get(), 'Setup failed');
		}
		$command = $this->buildCommand($project, $testPath);
		$this->executeTestCommand($command);
		foreach([self::TmpFileStderrPrefix, self::TmpFileStdoutPrefix] as $stream) {
			$this->filterOutput($stream, $testPath);
			$this->compareOutput($stream, $testPath);
		}
		if ($this->teardown->get()) {
			$this->executeSystemCommand($this->teardown->get(), 'Teardown failed');
		}
	}

	/**
	 * Execute $command using `system`
	 * If the return code differs from what is expected (field expectedReturnCode), the function throws
	 * @param string $command the shell command to execute project's binary
	 */
	protected function executeTestCommand(string $command): void
	{
		$expected = $this->expectedReturnCode->get();
		try {
			$this->executeSystemCommand($command, null, $expected);
		} catch (Exception $e) {
			$exceptionMsgSplit = explode(' ', $e->getMessage());
			$actualReturnCode = end($exceptionMsgSplit);
			throw new MarvinetteException("Returned $actualReturnCode instead of $expected");
		}
	}

	/**
	 * Calls `system` function passing $command as parameter
	 * If the return code differs from $expectedReturnCode, throws
	 * @param string $command shell command
	 * @param int $expectedReturnCode If the return code differs from it, the function hrows
	 * @param string $message what the exception message should contain. The return code will be inserted after
	 */
	protected function executeSystemCommand(string $command, ?string $message = null, ?int $expectedReturnCode = 0): void
	{
		$actualReturnCode = 0;
		system($command, $actualReturnCode);
		if (is_null($expectedReturnCode)) {
			return;
		}
		if ($actualReturnCode != $expectedReturnCode) {
			$exceptionMsg = "Return code: $actualReturnCode";
			if ($message) {
				$exceptionMsg = "$message $exceptionMsg";
			}
			throw new MarvinetteException($exceptionMsg);
		}
	}

	/**
	 * @return string the full command to execute command as a string
	 */
	private function buildCommand(Project $project, string $testPath): string
	{
		$interpreter = $project->interpreter->get();
		$command = $project->binaryPath->get() . DIRECTORY_SEPARATOR . $project->binaryName->get();
		$stdinputPath = FileManager::normalizePath("$testPath/stdinput");
		if ($this->commandLineArguments->get()) {
			$command .= ' ' . $this->commandLineArguments->get();
		}
		if ($interpreter != null) {
			$interpreterCommand = $project->getInterpreterFullPath();
			if ($this->interpreterArguments->get()) {
				$interpreterCommand .= ' ' . $this->interpreterArguments->get();
			}
			$command = "$interpreterCommand $command";
		}
		if ($this->emptyEnv->get()) {
			$command = "env -i $command";
		}
		if ($this->stdinput->get() && file_exists($stdinputPath)) {
			$command = "cat '$stdinputPath' | ($command)";
		}
		$command .= ' > ' . self::TmpFileFolder . '/' . self::TmpFilePrefix . self::TmpFileStdoutPrefix;
		$command .= ' 2> ' . self::TmpFileFolder . '/' . self::TmpFilePrefix . self::TmpFileStderrPrefix;
		return FileManager::normalizePath($command);
	}

	/**
	 * Compare the actual output with what is expected
	 * @param string $streamName tells waht stream to compare (can be either `TmpFileStderrPrefix` or `TmpFileStdoutPrefix`)
	 * @param string $testPath where the test's files are located
	 */
	private function compareOutput(string $streamName, string $testPath): void
	{
		if (!in_array($streamName, [self::TmpFileStderrPrefix, self::TmpFileStdoutPrefix])) {
			throw new MarvinetteException("compareOutput: '$streamName' is an invalid stream name");
		}
		$expectedFieldName = "expected$streamName";
		if (!$this->$expectedFieldName->get()) {
			return;
		}
		$expectedOutputFile = FileManager::normalizePath("$testPath/expected$streamName");
		$actualOutputFile = FileManager::normalizePath(self::TmpFileFolder . '/' . self::TmpFilePrefix . $streamName);
		$diffOutputFile = FileManager::normalizePath(self::TmpFileFolder . '/' . self::TmpFilePrefix . self::TmpDiffFilePrefix);
		$this->executeSystemCommand("diff --strip-trailing-cr '$expectedOutputFile' '$actualOutputFile' > $diffOutputFile", "Expected Output differs.");
	}

	/**
	 * Filter the output if a filter is specified
	 * @param string $streamName tells waht stream to compare (can be either `TmpFileStderrPrefix` or `TmpFileStdoutPrefix`)
	 * @param string $testPath where the test's files are located
	 */
	private function filterOutput(string $streamName, string $testPath): void
	{
		if (!in_array($streamName, [self::TmpFileStderrPrefix, self::TmpFileStdoutPrefix])) {
			throw new MarvinetteException("filterOutput: '$streamName' is an invalid stream name");
		}
		$FilterFieldName = strtolower($streamName) . 'Filter';
		if (!$this->$FilterFieldName->get()) {
			return;
		}
		$actualOutputFilePath = FileManager::normalizePath(self::TmpFileFolder . '/' . self::TmpFilePrefix . $streamName);
		$filterCommand = file_get_contents(FileManager::normalizePath("$testPath/$FilterFieldName"));
		$tmpFilterFile = FileManager::normalizePath(self::TmpFileFolder . '/' . self::TmpFilePrefix . 'Filtered' . $streamName);
		$command = "(cat '$actualOutputFilePath' | $filterCommand) > $tmpFilterFile";
		$this->executeSystemCommand($command, "$streamName Filtering failed");
		$this->executeSystemCommand("mv '$tmpFilterFile' '$actualOutputFilePath'", "$streamName Filtering failed");
	}

	/**
	 * @brief The name of the test, will be the name of the folder holding the test's files
	 * @var string
	 */
	public Field $name;

	/**
	 * @brief The arguements to send to the programm
	 * @var ?string
	*/
	public Field $commandLineArguments;

	/**
	 * @brief The arguements to send to the interpreter if exists
	 * @var ?string
	*/
	public Field $interpreterArguments;

	/**
	 * @brief The return code expected at the end of the test, beteen 0 and 255
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
	 * @brief if true, the test should have to read from stdin, an empty file to fill will be created
	 * @var bool
	 */
	public Field $stdinput;

	/**
	 * @brief if true, the tested program will be executed with an empty env
	 * @var bool
	 */
	public Field $emptyEnv;

	/**
	 * @brief if true, will compare program's stdout to file filled by the user
	 * @var bool
	 */
	public Field $expectedStdout;

	/**
	 * @brief if true, will compare program's stderr to file filled by the user
	 * @var bool
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
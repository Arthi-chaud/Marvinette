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

		$this->commandLineArguments = new Field(function($args) {});

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
		}, "Leave empty to ignore");

		$this->stdoutFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner']);

		$this->stderrFilter = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner']);

		$this->stdin = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"By default no. If you say yes, an empty file will be created so you can set what to write on stdin");
		$this->expectedStdout = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"By default no. If you say yes, an empty file will be created so you can set what to expect on stdout");
		$this->expectedStderr = new Field(
			[Field::class, 'YesNoErrorHandler'],
			[Field::class, 'YesNoDataCleaner'],
			"By default no. If you say yes, an empty file will be created so you can set what to expect on stderr");
		
		$this->setup = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "Command to execute before executing program");

		$this->teardown = new Field(function($_) {}, [Field::class, 'EmptyDataCleaner'], "Command to execute after program's execution");
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
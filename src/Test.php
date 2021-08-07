<?php

/**
 * @brief Object representing the test's instruction
*/
class Test
{
    /**
     * @brief The name of the test
     * @var string
     */
    protected $name;

    /**
	 * @brief The arguements to send to the programm
	 * @var array
	*/
    protected $projectArgs;

    /**
	 * @brief The return code expected at the end of the test
	 * @var array
	*/
    protected $expectedReturnCode = 0;

    /**
     * @brief command (and args) to execute piped to the std output of the program
     * @var array
     */
    protected ?string $stdoutFilter = null;

    /**
     * @brief command (and args) to execute piped to the error output of the program
     * @var array
     */
    protected ?string $stderrFilter = null;

    /**
     * @brief path to the file with what should be read on the std input (the file will be copied)
     * @var string
     */
    protected ?string $stdinput = null;

    /**
     * @brief path to the file with what is expected on the stdout (the file will be copied)
     * @warn if none, or invalid, an empty file will be created and the user would have to fill it
     * @var string
     */
    protected ?string $expectedStdout = null;

    /**
     * @brief path to the file with what is expected on the stdout (the file will be copied)
     * @warn if none, or invalid, no file will be created
     * @var string
     */
    protected ?string $expectedStderr = null;

    /**
     * @brief command to execute before test
     * @var string
     */
    protected ?string $setup = null;

    /**
     * @brief command to execute after test
     * @var string
     */
    protected ?string $teardown = null;
}
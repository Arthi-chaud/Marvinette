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
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of projectArgs
     */ 
    public function getProjectArgs()
    {
        return $this->projectArgs;
    }

    /**
     * Set the value of projectArgs
     *
     * @return  self
     */ 
    public function setProjectArgs($projectArgs)
    {
        $this->projectArgs = $projectArgs;

        return $this;
    }

    /**
     * Get the value of expectedReturnCode
     *
     * @return  array
     */ 
    public function getExpectedReturnCode()
    {
        return $this->expectedReturnCode;
    }

    /**
     * Set the value of expectedReturnCode
     *
     * @param  array  $expectedReturnCode
     *
     * @return  self
     */ 
    public function setExpectedReturnCode(array $expectedReturnCode)
    {
        $this->expectedReturnCode = $expectedReturnCode;

        return $this;
    }

    /**
     * Get the value of stdoutFilter
     *
     * @return  array
     */ 
    public function getStdoutFilter()
    {
        return $this->stdoutFilter;
    }

    /**
     * Set the value of stdoutFilter
     *
     * @param  array  $stdoutFilter
     *
     * @return  self
     */ 
    public function setStdoutFilter(array $stdoutFilter)
    {
        $this->stdoutFilter = $stdoutFilter;

        return $this;
    }

    /**
     * Get the value of stderrFilter
     *
     * @return  array
     */ 
    public function getStderrFilter()
    {
        return $this->stderrFilter;
    }

    /**
     * Set the value of stderrFilter
     *
     * @param  array  $stderrFilter
     *
     * @return  self
     */ 
    public function setStderrFilter(array $stderrFilter)
    {
        $this->stderrFilter = $stderrFilter;

        return $this;
    }

    /**
     * Get the value of stdinput
     *
     * @return  string
     */ 
    public function getStdinput()
    {
        return $this->stdinput;
    }

    /**
     * Set the value of stdinput
     *
     * @param  string  $stdinput
     *
     * @return  self
     */ 
    public function setStdinput(string $stdinput)
    {
        $this->stdinput = $stdinput;

        return $this;
    }

    /**
     * Get the value of expectedStdout
     *
     * @return  string
     */ 
    public function getExpectedStdout()
    {
        return $this->expectedStdout;
    }

    /**
     * Set the value of expectedStdout
     *
     * @param  string  $expectedStdout
     *
     * @return  self
     */ 
    public function setExpectedStdout(string $expectedStdout)
    {
        $this->expectedStdout = $expectedStdout;

        return $this;
    }

    /**
     * Get the value of expectedStderr
     *
     * @return  string
     */ 
    public function getExpectedStderr()
    {
        return $this->expectedStderr;
    }

    /**
     * Set the value of expectedStderr
     *
     * @param  string  $expectedStderr
     *
     * @return  self
     */ 
    public function setExpectedStderr(string $expectedStderr)
    {
        $this->expectedStderr = $expectedStderr;

        return $this;
    }
}
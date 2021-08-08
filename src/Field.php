<?php

/**
 * @brief Object representing a class's variable member, but allows error handling and prompt help messages
*/
class Field
{
    private function defaultDataCleaner($data)
    {
        return $data;
    }

    /**
     * @brief A Field constructor, should be called in constructor of class
     * @param callable $errorHandler the function to handle error: must throw when error occurs
     * @param callable $datacleaner the function to clean the data (can be null)
     * @param string $promptHelp a helpe message, which will be displayed when the user is prompted to type the field
     */
    public function __construct(callable $errorHandler, callable $dataCleaner = null, ?string $promptHelp = null)
    {
        $this->errorHandler = $errorHandler;
        $this->dataCleaner = $dataCleaner;
        $this->promptHelp = $promptHelp;
    }

    /**
     * @brief the data in itself, by default is null
     * @var mixed
     */
    protected $data = null;

    /**
     * @brief the function to handle errrors
     * @info throw on error or return void
     * @var callable
     */
    protected $errorHandler;

    /**
     * @brief the function to clean data
     * @info return the cleaned data
     * @var callable
     */
    protected $dataCleaner;

    /**
     * @brief an array of string to display on prompt to help the user
     * @var array
     */
    protected $promptHelp = [];

    /**
     * Get the value of data
     *
     * @return  mixed
     */ 
    public function get()
    {
        return $this->data;
    }

    /**
     * @brief call setter
     */
    public function set($data): void
    {
        ($this->errorHandler)($data);
        if ($this->dataCleaner)
            $data = ($this->dataCleaner)($data);
        $this->data = $data;
    }

    /**
     * Get prompt helper
     *
     * @return  array
     */ 
    public function getPromptHelp()
    {
        return $this->promptHelp;
    }
}
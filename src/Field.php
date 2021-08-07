<?php

/**
 * @brief Object representing a class's variable member, but allows error handling and prompt help messages
*/
class Field
{

    public static function defaultDataCleaner($data)
    {
        return $data;
    }

    public function __construct($errorHandler, $dataCleaner = Field::defaultDataCleaner, $promptHelp = [])
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
        ($this->setter)($data);
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
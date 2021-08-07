<?php

/**
 * @brief Object representing a class's variable member, but allows error handling and prompt help messages
*/
class Field
{

    public function __construct($setter, $promptHelp = [])
    {
        $this->setter = $setter;
        $this->promptHelp = $promptHelp;
    }
    /**
     * @brief the data in itself, by default is null
     * @var mixed
     */
    protected $data = null;

    /**
     * @brief the function to set the data, where all the error handling is done
     * @info it must trow on error and return void
     * @var callable
     */
    protected $setter;

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
<?php

Class CLIOption
{
    public static function get($options): array
    {
        $shortopt = "";
        $longopts = [];
        foreach ($options as $option) {
            if (strlen($option) == 1)
                $shortopt .= $option;
            else
                $longopts .= $options;
        }
        return getopt($shortopt, $longopts);
    }
}
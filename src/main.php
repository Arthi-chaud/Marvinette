#!/bin/php
<?php

require_once 'src/Marvinette.php';

if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
    $marvinette = new Marvinette();
    $marvinette->modProject();
    $marvinette->deleteProject();
}
return 1;
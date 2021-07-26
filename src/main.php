<?php



if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
    return main();
}
return 1;
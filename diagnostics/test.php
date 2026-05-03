<?php

header('Content-Type: text/plain');
echo "yes, this is a test file";
var_dump(getenv('WYP_DIAG_KEY'));
var_dump($_SERVER['WYP_DIAG_KEY'] ?? null);
var_dump($_ENV['WYP_DIAG_KEY'] ?? null);


if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_env', $modules)) {
        echo "mod_env is enabled!";
    } else {
        echo "mod_env is NOT enabled.";
    }
} else {
    echo "PHP is not running as an Apache module.";
}

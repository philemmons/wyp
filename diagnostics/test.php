<?php

header('Content-Type: text/plain');
var_dump(getenv('WYP_DIAG_KEY'));
var_dump($_SERVER['WYP_DIAG_KEY'] ?? null);
var_dump($_ENV['WYP_DIAG_KEY'] ?? null);
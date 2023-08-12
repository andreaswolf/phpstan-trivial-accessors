<?php

$config = new PhpCsFixer\Config();
$config
    ->getFinder()->in([
        'src/',
        'tests/',
    ]);

return $config;
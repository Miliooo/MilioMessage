<?php

if (file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
    $loader = require $file;
    $loader->add('Milio\Message', __DIR__);
} else {
    throw new RuntimeException('Install dependencies to run test suite.');
}
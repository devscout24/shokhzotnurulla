<?php
require "vendor/autoload.php";
try {
    $r = new ReflectionClass("Intervention\Image\Laravel\ServiceProvider");
    echo "Constructor params: " . $r->getConstructor()->getNumberOfRequiredParameters() . PHP_EOL;
    echo "File: " . $r->getFileName() . PHP_EOL;
    $instance = $r->newInstanceArgs([null]);
    echo "Instance created OK" . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}


<?php
if (version_compare(PHP_VERSION, '5.4') < 0) {
    throw new \Exception('scssphp requires PHP 5.4 or above');
}

if (! class_exists('scssc', false)) {
    include_once __DIR__ . '/src/Colors.php';
    include_once __DIR__ . '/src/Compiler.php';
    include_once __DIR__ . '/src/Formatter.php';
    include_once __DIR__ . '/src/Formatter/Compressed.php';
    include_once __DIR__ . '/src/Formatter/Crunched.php';
    include_once __DIR__ . '/src/Formatter/Expanded.php';
    include_once __DIR__ . '/src/Formatter/Nested.php';
    include_once __DIR__ . '/src/Parser.php';
    include_once __DIR__ . '/src/Server.php';
}

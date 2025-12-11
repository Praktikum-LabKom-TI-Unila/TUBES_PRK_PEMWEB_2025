<?php
/**
 * SiPaMaLi - Main Entry Point
 * Front Controller for PHP Built-in Server
 */

// Change working directory to src/
chdir(__DIR__ . '/src');

// Require the actual router
require __DIR__ . '/src/index.php';

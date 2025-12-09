<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/src/app/config/config.php';
require BASE_PATH . '/src/app/config/database.php';
require BASE_PATH . '/src/app/core/App.php';
require BASE_PATH . '/src/app/core/Controller.php';
require BASE_PATH . '/src/app/core/Model.php';

require BASE_PATH . '/src/app/helpers/auth_helper.php';
require BASE_PATH . '/src/app/helpers/product_helper.php';

spl_autoload_register(function ($class) {
  $paths = [
    BASE_PATH . '/src/app/controllers/' . $class . '.php',
    BASE_PATH . '/src/app/models/' . $class . '.php',
  ];

  foreach ($paths as $file) {
    if (file_exists($file)) {
      require $file;
      return;
    }
  }
});

$app = new App();

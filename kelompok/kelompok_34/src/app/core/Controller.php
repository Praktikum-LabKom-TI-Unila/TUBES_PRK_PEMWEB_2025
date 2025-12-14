<?php

class Controller
{
  public function model($model)
  {
    $modelClass = ucfirst($model);
    $path = BASE_PATH . '/src/app/models/' . $modelClass . '.php';
    if (file_exists($path)) {
      require_once $path;
      return new $modelClass;
    }
    throw new Exception("Model {$modelClass} tidak ditemukan");
  }

  public function view($view, $data = [])
  {
    $viewPath = BASE_PATH . '/src/app/views/' . $view . '.php';
    if (!file_exists($viewPath)) {
      http_response_code(404);
      $viewPath = BASE_PATH . '/src/app/views/errors/404.php';
    }
    extract($data);
    require BASE_PATH . '/src/app/views/layouts/header.php';
    require $viewPath;
    require BASE_PATH . '/src/app/views/layouts/footer.php';
  }

  public function plainView($view, $data = [])
  {
    $viewPath = BASE_PATH . '/src/app/views/' . $view . '.php';
    if (!file_exists($viewPath)) {
      http_response_code(404);
      $viewPath = BASE_PATH . '/src/app/views/errors/404.php';
    }
    extract($data);
    require $viewPath;
  }
}

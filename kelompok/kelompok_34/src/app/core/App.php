<?php

class App
{
  protected $controller = 'PosController';
  protected $method = 'index';
  protected $params = [];

  // File: src/app/core/App.php

public function __construct()
{
  $url = $this->parseUrl();

  // UBAH DARI $url[4] MENJADI $url[0]
  if (isset($url[0]) && file_exists(BASE_PATH . '/src/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
    $this->controller = ucfirst($url[0]) . 'Controller';
    unset($url[0]); // UBAH INI JUGA JADI 0
  }

  require BASE_PATH . '/src/app/controllers/' . $this->controller . '.php';
  $this->controller = new $this->controller;

  // UBAH DARI $url[5] MENJADI $url[1]
  if (isset($url[1]) && method_exists($this->controller, $url[1])) {
    $this->method = $url[1]; // UBAH INI JUGA JADI 1
    unset($url[1]); // UBAH INI JUGA JADI 1
  }

  $this->params = $url ? array_values($url) : [];

  call_user_func_array([$this->controller, $this->method], $this->params);
}

  private function parseUrl()
  {
    if (isset($_GET['url'])) {
      return explode(
        '/',
        filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)
      );
    }

    return ['pos'];
  }
}

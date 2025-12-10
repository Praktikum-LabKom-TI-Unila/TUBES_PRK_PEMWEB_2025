<?php

class App
{
  protected $controller = 'PosController';
  protected $method = 'index';
  protected $params = [];

  public function __construct()
  {
    $url = $this->parseUrl();

    if (isset($url[0]) && file_exists(BASE_PATH . '/src/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
      $this->controller = ucfirst($url[0]) . 'Controller';
      unset($url[0]);
    }

    require BASE_PATH . '/src/app/controllers/' . $this->controller . '.php';
    $this->controller = new $this->controller;

    if (isset($url[1]) && method_exists($this->controller, $url[1])) {
      $this->method = $url[1];
      unset($url[1]);
    }

    $this->params = $url ? array_values($url) : [];

    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  private function parseUrl()
  {
    if (isset($_GET['url'])) {
      $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
      $segments = explode('/', $url);
      $index = array_search('src', $segments);

      if ($index !== false) {
        $segments = array_slice($segments, $index + 1);
      }

      return $segments;
    }

    return ['pos'];
  }
}

<?php

class HomeController extends Controller
{
  public function index()
  {
    $data = [
      'title' => APP_NAME . " - Home",
    ];

    $this->view('home/index', $data);
  }
}

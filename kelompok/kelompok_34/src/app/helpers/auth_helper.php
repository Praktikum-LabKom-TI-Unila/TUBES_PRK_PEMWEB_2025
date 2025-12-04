<?php

function isAuthenticated()
{
  return isset($_SESSION['user']);
}

function requireAuth()
{
  session_start();
  if (!isAuthenticated()) {
    header("Location: " . BASE_URL . "/auth/login");
    exit;
  }
}

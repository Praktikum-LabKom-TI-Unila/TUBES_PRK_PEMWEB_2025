<?php

class HelpController extends Controller
{
  public function index()
  {
    $data['title'] = 'Pusat Bantuan - ' . APP_NAME;
    $this->view('help/index', $data);
  }

  public function sellerGuide()
  {
    $data['title'] = 'Panduan Penjual - ' . APP_NAME;
    $this->view('help/seller-guide', $data);
  }

  public function adminGuide()
  {
    requireAuth();
    session_start();
    
    if ($_SESSION['user']['role'] !== 'admin') {
      $_SESSION['error'] = 'Akses ditolak! Hanya admin yang bisa melihat panduan admin.';
      header("Location: " . BASE_URL . "/help");
      exit;
    }

    $data['title'] = 'Panduan Admin - ' . APP_NAME;
    $this->view('help/admin-guide', $data);
  }

  public function faq()
  {
    $data['title'] = 'FAQ - ' . APP_NAME;
    $this->view('help/faq', $data);
  }
}
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
    requireAdmin("/help", 'Akses ditolak! Hanya admin yang bisa melihat panduan admin.');
    $data['title'] = 'Panduan Admin - ' . APP_NAME;
    $this->view('help/admin-guide', $data);
  }

  public function faq()
  {
    $data['title'] = 'FAQ - ' . APP_NAME;
    $this->view('help/faq', $data);
  }
}

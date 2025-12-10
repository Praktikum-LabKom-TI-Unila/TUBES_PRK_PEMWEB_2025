<?php 
if (!isset($assetPath)) {
  $assetPath = "../../assets/";
}
?>

<footer class="sb-footer mt-5">
  <div class="container text-center">

    <!-- Logo Text (tanpa gambar, sama style seperti navbar) -->
    <div class="footer-logo-text">ScholarBridge</div>

    <p class="footer-title">
      Platform Bimbingan Belajar Lampung
    </p>

    <small class="footer-copy">
      © <?php echo date("Y"); ?> ScholarBridge. All Rights Reserved.
    </small>

    <div class="footer-links mt-3">
      <a href="#">Kebijakan</a>
      <a href="#">Bantuan</a>
      <a href="#">Tentang Kami</a>
    </div>

  </div>
</footer>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script src="<?php echo $assetPath ?>js/script.js"></script>
<script src="<?php echo $assetPath ?>js/ajax_search.js"></script>

</body>
</html>

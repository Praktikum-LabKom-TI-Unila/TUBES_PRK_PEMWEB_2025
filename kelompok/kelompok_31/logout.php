<?php
/**
 * Logout Handler
 * Dikerjakan oleh: Anggota 1
 */

session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit();
?>

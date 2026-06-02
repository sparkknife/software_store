<?php
session_start();
session_destroy();
header('Location: /software_store/login.php');
exit;
?>
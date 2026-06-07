<?php
// خروج کامل کاربر از حساب و پاک کردن session
session_start();
session_unset();
session_destroy();

// بعد از خروج، کاربر به صفحه ورود برگردد
header('Location: /software_store/login.php');
exit;

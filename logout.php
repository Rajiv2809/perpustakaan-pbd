<?php
// logout.php
session_start();
session_destroy();
header('Location: /perpustakaan/login.php');
exit;

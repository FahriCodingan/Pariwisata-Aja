<?php
session_start();
session_destroy();

header("Location: loginNyaAdmin.php");
exit;
?>
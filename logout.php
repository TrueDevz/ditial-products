<?php
// logout.php
session_start();
session_unset();
session_destroy();
header('Location: /digitalProducts/index.php');
exit;
?>

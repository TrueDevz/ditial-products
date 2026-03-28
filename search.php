<?php
// search.php
$q = $_GET['q'] ?? '';
header('Location: /digitalProducts/category.php?q=' . urlencode($q));
exit;
?>

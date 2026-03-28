<?php
// search.php
$q = $_GET['q'] ?? '';
header('Location: ' . BASE_URL . '/category.php?q=' . urlencode($q));
exit;
?>

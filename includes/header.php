<?php
if (!isset($page_title)) $page_title = 'StudyVault';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | StudyVault</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php">StudyVault</a>
  <nav>
    <a href="index.php">Notes</a>
    <?php if (is_logged_in()): ?>
      <a href="upload.php">Upload</a>
      <?php if (($_SESSION['role'] ?? '') === 'admin'): ?><a href="admin/index.php">Admin</a><?php endif; ?>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a class="button small" href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php show_flash(); ?>

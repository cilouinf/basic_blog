<?php
// If direct access
if(!file_exists('classes/DB.php')) {
    header('Location: ../index.php');
    exit();
}
?>
<!doctype html>
<html lang="zxx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=$windowTitle ?? 'Blog'?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fa-svg-with-js.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Start NavBar -->

    <nav class="navbar navbar-expand navbar-light bg-light">
        <a class="navbar-brand">Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">S'authentifier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Créer un compte</a>
                </li>
            </ul>
        </div>
        <form class="form-inline" action="<?= basename($_SERVER['PHP_SELF']); ?>?" method="get">
            <input class="form-control mr-sm-2" type="search" placeholder="Rechercher un article" aria-label="Recherche" name="search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Recherche</button>
        </form>
    </nav>

<!-- End navBar -->
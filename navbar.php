<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
function navActive($page)
{
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Hotel</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Hotel Local</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link <?php echo navActive('index.php'); ?>" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?php echo navActive('quartos.php'); ?>" href="quartos.php">Quartos</a></li>
                <li class="nav-item"><a class="nav-link <?php echo navActive('hospedes.php'); ?>" href="hospedes.php">Hóspedes</a></li>
                <li class="nav-item"><a class="nav-link <?php echo navActive('reservas.php'); ?>" href="reservas.php">Reservas</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">

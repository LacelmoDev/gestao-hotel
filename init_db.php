<?php
require_once __DIR__ . '/db.php';
init_db();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicializar Banco de Dados</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success">
        Banco de dados inicializado com sucesso.<br>
        <strong>Arquivo:</strong> data/hotel.db
    </div>
    <a href="index.php" class="btn btn-primary">Ir para o sistema</a>
</div>
</body>
</html>

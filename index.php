<?php
require_once __DIR__ . '/db.php';
init_db();
$rooms = $pdo->query('SELECT COUNT(*) FROM quartos')->fetchColumn();
$guests = $pdo->query('SELECT COUNT(*) FROM hospedes')->fetchColumn();
$reservations = $pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn();
$active = $pdo->query('SELECT COUNT(*) FROM reservas WHERE status = "reserved"')->fetchColumn();
?>
<?php include 'header.php'; ?>
<div class="row mb-4">
    <div class="col-12">
        <h1>Dashboard</h1>
        <p class="text-muted">Sistema de gestão de hotel local usando PHP, SQLite e Bootstrap.</p>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Quartos</h5>
                <p class="card-text display-6"><?php echo $rooms; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Hóspedes</h5>
                <p class="card-text display-6"><?php echo $guests; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Reservas</h5>
                <p class="card-text display-6"><?php echo $reservations; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Ativas</h5>
                <p class="card-text display-6"><?php echo $active; ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

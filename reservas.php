<?php
require_once __DIR__ . '/db.php';
init_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'cancel') {
        $stmt = $pdo->prepare('UPDATE reservas SET status = "cancelled" WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        $stmt = $pdo->prepare('UPDATE quartos SET status = "available" WHERE id = (SELECT room_id FROM reservas WHERE id = ?)');
        $stmt->execute([$_POST['id']]);
        $message = 'Reserva cancelada.';
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'checkout') {
        $stmt = $pdo->prepare('UPDATE reservas SET status = "checked_out" WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        $stmt = $pdo->prepare('UPDATE quartos SET status = "available" WHERE id = (SELECT room_id FROM reservas WHERE id = ?)');
        $stmt->execute([$_POST['id']]);
        $message = 'Check-out registrado.';
    } else {
        $guest_id = $_POST['guest_id'];
        $room_id = $_POST['room_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $notes = trim($_POST['notes']);

        $stmt = $pdo->prepare('INSERT INTO reservas (guest_id, room_id, check_in, check_out, notes) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$guest_id, $room_id, $check_in, $check_out, $notes]);
        $stmt = $pdo->prepare('UPDATE quartos SET status = "occupied" WHERE id = ?');
        $stmt->execute([$room_id]);
        $message = 'Reserva criada com sucesso.';
    }
}

$reservations = $pdo->query('SELECT r.*, g.name AS guest_name, ro.number AS room_number FROM reservas r
    JOIN hospedes g ON r.guest_id = g.id
    JOIN quartos ro ON r.room_id = ro.id
    ORDER BY r.check_in DESC')->fetchAll(PDO::FETCH_ASSOC);
$guests = $pdo->query('SELECT * FROM hospedes ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$rooms = $pdo->query('SELECT * FROM quartos WHERE status = "available" ORDER BY number')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="row mb-4">
    <div class="col-12">
        <h1>Reservas</h1>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Nova reserva</h5>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Hóspede</label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Selecione um hóspede</option>
                            <?php foreach ($guests as $guest): ?>
                                <option value="<?php echo $guest['id']; ?>"><?php echo htmlspecialchars($guest['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quarto</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Selecione um quarto</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['number'] . ' - ' . $room['type']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-in</label>
                        <input type="date" name="check_in" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-out</label>
                        <input type="date" name="check_out" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar reserva</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Lista de reservas</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Hóspede</th>
                            <th>Quarto</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['check_in']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['check_out']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                                <td>
                                    <?php if ($reservation['status'] === 'reserved'): ?>
                                        <form method="post" class="d-inline-block">
                                            <input type="hidden" name="action" value="checkout">
                                            <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Check-out</button>
                                        </form>
                                        <form method="post" class="d-inline-block">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Sem ações</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

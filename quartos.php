<?php
require_once __DIR__ . '/db.php';
init_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM quartos WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        $message = 'Quarto removido com sucesso.';
    } else {
        $number = trim($_POST['number']);
        $type = trim($_POST['type']);
        $price = floatval($_POST['price']);
        $status = trim($_POST['status']);

        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare('UPDATE quartos SET number = ?, type = ?, price = ?, status = ? WHERE id = ?');
            $stmt->execute([$number, $type, $price, $status, $_POST['id']]);
            $message = 'Quarto atualizado com sucesso.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO quartos (number, type, price, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$number, $type, $price, $status]);
            $message = 'Quarto criado com sucesso.';
        }
    }
}

$editRoom = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM quartos WHERE id = ?');
    $stmt->execute([$_GET['edit']]);
    $editRoom = $stmt->fetch(PDO::FETCH_ASSOC);
}

$rooms = $pdo->query('SELECT * FROM quartos ORDER BY number')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="row mb-4">
    <div class="col-12">
        <h1>Quartos</h1>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $editRoom ? 'Editar quarto' : 'Cadastrar quarto'; ?></h5>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $editRoom ? $editRoom['id'] : ''; ?>">
                    <div class="mb-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" class="form-control" required value="<?php echo $editRoom ? htmlspecialchars($editRoom['number']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="type" class="form-control" required value="<?php echo $editRoom ? htmlspecialchars($editRoom['type']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preço (AOA)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?php echo $editRoom ? htmlspecialchars($editRoom['price']) : '0.00'; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="available" <?php echo ($editRoom && $editRoom['status'] === 'available') ? 'selected' : ''; ?>>Disponível</option>
                            <option value="occupied" <?php echo ($editRoom && $editRoom['status'] === 'occupied') ? 'selected' : ''; ?>>Ocupado</option>
                            <option value="maintenance" <?php echo ($editRoom && $editRoom['status'] === 'maintenance') ? 'selected' : ''; ?>>Manutenção</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $editRoom ? 'Atualizar' : 'Salvar'; ?></button>
                    <?php if ($editRoom): ?>
                        <a href="quartos.php" class="btn btn-secondary ms-2">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Lista de quartos</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['number']); ?></td>
                                <td><?php echo htmlspecialchars($room['type']); ?></td>
                                <td>AOA <?php echo number_format($room['price'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($room['status']); ?></td>
                                <td>
                                    <a href="quartos.php?edit=<?php echo $room['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form method="post" class="d-inline-block" onsubmit="return confirm('Remover este quarto?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
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

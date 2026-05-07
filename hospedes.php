<?php
require_once __DIR__ . '/db.php';
init_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM hospedes WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        $message = 'Hóspede removido com sucesso.';
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $document = trim($_POST['document']);

        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare('UPDATE hospedes SET name = ?, email = ?, phone = ?, document = ? WHERE id = ?');
            $stmt->execute([$name, $email, $phone, $document, $_POST['id']]);
            $message = 'Hóspede atualizado com sucesso.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO hospedes (name, email, phone, document) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $phone, $document]);
            $message = 'Hóspede cadastrado com sucesso.';
        }
    }
}

$editGuest = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM hospedes WHERE id = ?');
    $stmt->execute([$_GET['edit']]);
    $editGuest = $stmt->fetch(PDO::FETCH_ASSOC);
}

$guests = $pdo->query('SELECT * FROM hospedes ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="row mb-4">
    <div class="col-12">
        <h1>Hóspedes</h1>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $editGuest ? 'Editar hóspede' : 'Cadastrar hóspede'; ?></h5>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $editGuest ? $editGuest['id'] : ''; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo $editGuest ? htmlspecialchars($editGuest['name']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $editGuest ? htmlspecialchars($editGuest['email']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $editGuest ? htmlspecialchars($editGuest['phone']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documento</label>
                        <input type="text" name="document" class="form-control" value="<?php echo $editGuest ? htmlspecialchars($editGuest['document']) : ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $editGuest ? 'Atualizar' : 'Salvar'; ?></button>
                    <?php if ($editGuest): ?>
                        <a href="hospedes.php" class="btn btn-secondary ms-2">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Lista de hóspedes</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Documento</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($guests as $guest): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($guest['name']); ?></td>
                                <td><?php echo htmlspecialchars($guest['email']); ?></td>
                                <td><?php echo htmlspecialchars($guest['phone']); ?></td>
                                <td><?php echo htmlspecialchars($guest['document']); ?></td>
                                <td>
                                    <a href="hospedes.php?edit=<?php echo $guest['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form method="post" class="d-inline-block" onsubmit="return confirm('Remover este hóspede?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $guest['id']; ?>">
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

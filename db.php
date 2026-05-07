<?php
$dbFile = __DIR__ . '/data/hotel.db';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON;');

function tableExists($name)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type = 'table' AND name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn() !== false;
}

function ensureTable($name, $sql)
{
    global $pdo;
    if (!tableExists($name)) {
        $pdo->exec($sql);
    }
}

function init_db()
{
    global $pdo;

    if ((tableExists('rooms') || tableExists('quartos')) && !tableExists('quartos')) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS quartos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            number TEXT NOT NULL UNIQUE,
            type TEXT NOT NULL,
            price REAL NOT NULL,
            status TEXT NOT NULL DEFAULT "available"
        )');

        if (tableExists('rooms')) {
            $pdo->beginTransaction();
            $pdo->exec('PRAGMA foreign_keys = OFF;');
            $pdo->exec('INSERT INTO quartos (id, number, type, price, status) SELECT id, number, type, price, status FROM rooms');
            $pdo->exec('PRAGMA foreign_keys = ON;');
            $pdo->commit();
        }
    }

    if ((tableExists('guests') || tableExists('hospedes')) && !tableExists('hospedes')) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS hospedes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            document TEXT
        )');

        if (tableExists('guests')) {
            $pdo->beginTransaction();
            $pdo->exec('PRAGMA foreign_keys = OFF;');
            $pdo->exec('INSERT INTO hospedes (id, name, email, phone, document) SELECT id, name, email, phone, document FROM guests');
            $pdo->exec('PRAGMA foreign_keys = ON;');
            $pdo->commit();
        }
    }

    if (tableExists('reservations') && !tableExists('reservas')) {
        // Ensure target tables exist before copying reservations.
        ensureTable('quartos', 'CREATE TABLE IF NOT EXISTS quartos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            number TEXT NOT NULL UNIQUE,
            type TEXT NOT NULL,
            price REAL NOT NULL,
            status TEXT NOT NULL DEFAULT "available"
        )');
        ensureTable('hospedes', 'CREATE TABLE IF NOT EXISTS hospedes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            document TEXT
        )');

        $pdo->beginTransaction();
        $pdo->exec('PRAGMA foreign_keys = OFF;');
        $pdo->exec('CREATE TABLE IF NOT EXISTS reservas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            guest_id INTEGER NOT NULL,
            room_id INTEGER NOT NULL,
            check_in TEXT NOT NULL,
            check_out TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT "reserved",
            notes TEXT,
            FOREIGN KEY (guest_id) REFERENCES hospedes(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES quartos(id) ON DELETE CASCADE
        )');
        $pdo->exec('INSERT INTO reservas (id, guest_id, room_id, check_in, check_out, status, notes) SELECT id, guest_id, room_id, check_in, check_out, status, notes FROM reservations');
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->commit();
    }

    if (tableExists('reservations')) {
        $pdo->beginTransaction();
        $pdo->exec('PRAGMA foreign_keys = OFF;');
        $pdo->exec('DROP TABLE reservations');
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->commit();
    }

    if (tableExists('guests')) {
        $pdo->beginTransaction();
        $pdo->exec('PRAGMA foreign_keys = OFF;');
        $pdo->exec('DROP TABLE guests');
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->commit();
    }

    if (tableExists('rooms')) {
        $pdo->beginTransaction();
        $pdo->exec('PRAGMA foreign_keys = OFF;');
        $pdo->exec('DROP TABLE rooms');
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->commit();
    }

    ensureTable('quartos', 'CREATE TABLE IF NOT EXISTS quartos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        number TEXT NOT NULL UNIQUE,
        type TEXT NOT NULL,
        price REAL NOT NULL,
        status TEXT NOT NULL DEFAULT "available"
    )');

    ensureTable('hospedes', 'CREATE TABLE IF NOT EXISTS hospedes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT,
        phone TEXT,
        document TEXT
    )');

    ensureTable('reservas', 'CREATE TABLE IF NOT EXISTS reservas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        guest_id INTEGER NOT NULL,
        room_id INTEGER NOT NULL,
        check_in TEXT NOT NULL,
        check_out TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT "reserved",
        notes TEXT,
        FOREIGN KEY (guest_id) REFERENCES hospedes(id) ON DELETE CASCADE,
        FOREIGN KEY (room_id) REFERENCES quartos(id) ON DELETE CASCADE
    )');

    $count = $pdo->query('SELECT COUNT(*) FROM quartos')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO quartos (number, type, price, status) VALUES (?, ?, ?, ?)');
        $stmt->execute(['101', 'Single', 120.00, 'available']);
        $stmt->execute(['102', 'Double', 180.00, 'available']);
        $stmt->execute(['201', 'Suite', 280.00, 'available']);
    }
}

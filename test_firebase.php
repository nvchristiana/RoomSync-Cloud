<?php
require_once __DIR__ . '/firebase_db.php';

$db = getDatabase();

$db->getReference('test')->set([
    'pesan' => 'Koneksi berhasil',
    'waktu' => date('Y-m-d H:i:s'),
]);

echo "Data berhasil ditulis ke Firebase.\n";
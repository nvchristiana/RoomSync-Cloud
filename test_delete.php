<?php
require_once __DIR__ . '/booking.php';

$b = createBooking('uid-dummy-1', 'C', '2026-10-14', '09:00', '11:00');
if (!$b['ok']) {
    echo "Gagal membuat booking: " . $b['error'] . "\n";
    exit;
}
$id = $b['booking']['booking_id'];
echo "Booking dibuat: $id\n";

$h = cancelBooking('uid-lain', $id);
echo "Batal oleh bukan pemilik => " . ($h['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $h['error']) . "\n";

$h = cancelBooking('uid-dummy-1', $id);
echo "Batal oleh pemilik => " . ($h['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $h['error']) . "\n";

$h = cancelBooking('uid-dummy-1', $id);
echo "Batal booking yang sudah dihapus => " . ($h['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $h['error']) . "\n";

$c = createBooking('uid-dummy-1', 'C', '2026-10-14', '09:00', '11:00');
echo "Pesan ulang slot yang sama => " . ($c['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $c['error']) . "\n";
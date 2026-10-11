<?php
require_once __DIR__ . '/booking.php';

$kasus = [
    ['A', '10:00', '12:00'],  // seharusnya BERHASIL
    ['A', '10:00', '12:00'],  // seharusnya DITOLAK (sama persis)
    ['A', '11:00', '13:00'],  // seharusnya DITOLAK (tumpang tindih)
    ['A', '12:00', '14:00'],  // seharusnya BERHASIL (menempel, tidak bentrok)
    ['B', '10:00', '12:00'],  // seharusnya BERHASIL (ruang lain)
];

foreach ($kasus as [$ruang, $mulai, $selesai]) {
    $h = createBooking('uid-dummy-1', $ruang, '2026-10-13', $mulai, $selesai);
    echo "Ruang $ruang $mulai-$selesai => " . ($h['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $h['error']) . "\n";
}
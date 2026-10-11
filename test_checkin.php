<?php
require_once __DIR__ . '/booking.php';
require_once __DIR__ . '/checkin.php';

function tampil(string $label, array $h): void
{
    echo $label . ' => ' . ($h['ok'] ? 'BERHASIL' : 'DITOLAK: ' . $h['error']) . "\n";
}

$tz       = new DateTimeZone('Asia/Jakarta');
$sekarang = new DateTime('now', $tz);
$tanggal  = $sekarang->format('Y-m-d');
$mulai    = $sekarang->format('H:i');
$akhir    = (clone $sekarang)->modify('+30 minutes');
$selesai  = $akhir->format('Y-m-d') === $tanggal ? $akhir->format('H:i') : '23:59';
$besok    = (clone $sekarang)->modify('+1 day')->format('Y-m-d');
$kemarin  = (clone $sekarang)->modify('-1 day')->format('Y-m-d');

$a = createBooking('uid-dummy-1', 'A', $besok, '09:00', '10:00');
tampil('Check-in sebelum jam mulai', checkIn('uid-dummy-1', $a['booking']['booking_id']));

$c = createBooking('uid-dummy-1', 'C', $kemarin, '09:00', '10:00');
tampil('Check-in lewat batas 10 menit', checkIn('uid-dummy-1', $c['booking']['booking_id']));

$b  = createBooking('uid-dummy-1', 'B', $tanggal, $mulai, $selesai);
$id = $b['booking']['booking_id'];
tampil('Check-in oleh bukan pemilik', checkIn('uid-lain', $id));
tampil('Check-in oleh pemilik', checkIn('uid-dummy-1', $id));
tampil('Check-in ulang', checkIn('uid-dummy-1', $id));
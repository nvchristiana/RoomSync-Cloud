<?php
require_once __DIR__ . '/firebase_db.php';

function checkIn(string $uid, string $bookingId): array
{
    $ref     = getDatabase()->getReference('bookings/' . $bookingId);
    $booking = $ref->getValue();

    if (!$booking) {
        return ['ok' => false, 'error' => 'Booking tidak ditemukan.'];
    }
    if ($booking['uid'] !== $uid) {
        return ['ok' => false, 'error' => 'Hanya pemilik booking yang boleh check-in.'];
    }
    if ($booking['status'] !== 'booked') {
        return ['ok' => false, 'error' => 'Booking ini tidak bisa check-in lagi.'];
    }

    $sekarang = time();

    if ($sekarang < $booking['start_at']) {
        return ['ok' => false, 'error' => 'Check-in belum dibuka.'];
    }
    if ($sekarang > $booking['start_at'] + 600) {
        return ['ok' => false, 'error' => 'Batas check-in sudah lewat.'];
    }

    $ref->update([
        'status'        => 'checked_in',
        'checked_in_at' => $sekarang,
    ]);

    return ['ok' => true];
}
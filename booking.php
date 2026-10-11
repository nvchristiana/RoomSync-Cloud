<?php
require_once __DIR__ . '/firebase_db.php';

use Kreait\Firebase\Database\Transaction;
use Kreait\Firebase\Exception\Database\TransactionFailed;

function reserveSlot(string $roomId, string $bookingId, int $startTs, int $endTs): bool
{
    $db  = getDatabase();
    $ref = $db->getReference('schedules/' . $roomId);

    for ($try = 1; $try <= 3; $try++) {
        $berhasil = false;
        try {
            $db->runTransaction(function (Transaction $tx) use ($ref, $bookingId, $startTs, $endTs, &$berhasil) {
                $slots = $tx->snapshot($ref)->getValue() ?? [];

                foreach ($slots as $slot) {
                    if ($startTs < $slot['end_at'] && $endTs > $slot['start_at']) {
                        return;
                    }
                }

                $slots[$bookingId] = ['start_at' => $startTs, 'end_at' => $endTs];
                $tx->set($ref, $slots);
                $berhasil = true;
            });
            return $berhasil;
        } catch (TransactionFailed $e) {
        }
    }
    return false;
}

function createBooking(string $uid, string $roomId, string $date, string $start, string $end): array
{
    if (!in_array($roomId, ['A', 'B', 'C'], true)) {
        return ['ok' => false, 'error' => 'Ruang tidak valid.'];
    }

    $tz      = new DateTimeZone('Asia/Jakarta');
    $startAt = DateTime::createFromFormat('Y-m-d H:i', "$date $start", $tz);
    $endAt   = DateTime::createFromFormat('Y-m-d H:i', "$date $end", $tz);

    if (!$startAt || !$endAt) {
        return ['ok' => false, 'error' => 'Format tanggal atau jam salah.'];
    }
    if ($endAt <= $startAt) {
        return ['ok' => false, 'error' => 'Jam selesai harus setelah jam mulai.'];
    }

    $bookingId = 'RS-' . strtoupper(bin2hex(random_bytes(4)));
    $data = [
        'booking_id' => $bookingId,
        'room_id'    => $roomId,
        'uid'        => $uid,
        'start_at'   => $startAt->getTimestamp(),
        'end_at'     => $endAt->getTimestamp(),
        'status'     => 'booked',
        'created_at' => time(),
    ];

    if (!reserveSlot($roomId, $bookingId, $startAt->getTimestamp(), $endAt->getTimestamp())) {
        return ['ok' => false, 'error' => 'Slot sudah dipesan atau sedang diproses. Pilih jam lain.'];
    }

    getDatabase()->getReference('bookings/' . $bookingId)->set($data);

    return ['ok' => true, 'booking' => $data];
}
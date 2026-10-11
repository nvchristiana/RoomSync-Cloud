<?php
require_once __DIR__ . '/firebase_db.php';

date_default_timezone_set('Asia/Jakarta');

function getRoomStatuses(int $timestamp): array
{
    $statuses = ['A' => 'Tersedia', 'B' => 'Tersedia', 'C' => 'Tersedia'];
    $bookings = getDatabase()->getReference('bookings')->getValue() ?? [];

    foreach ($bookings as $b) {
        if ($timestamp < $b['start_at'] || $timestamp >= $b['end_at']) {
            continue;
        }
        $room = $b['room_id'];
        if ($b['status'] === 'checked_in') {
            $statuses[$room] = 'Sedang Digunakan';
        } elseif ($b['status'] === 'booked' && $statuses[$room] === 'Tersedia') {
            $statuses[$room] = 'Dipesan';
        }
    }

    return $statuses;
}

$tz   = new DateTimeZone('Asia/Jakarta');
$date = $_GET['date'] ?? date('Y-m-d');
$time = $_GET['time'] ?? date('H:i');
$dt   = DateTime::createFromFormat('Y-m-d H:i', "$date $time", $tz);

if (!$dt) {
    $dt   = new DateTime('now', $tz);
    $date = $dt->format('Y-m-d');
    $time = $dt->format('H:i');
}

$statuses = getRoomStatuses($dt->getTimestamp());
$kelas = [
    'Tersedia'         => 'bg-success',
    'Dipesan'          => 'bg-warning text-dark',
    'Sedang Digunakan' => 'bg-danger',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RoomSync Cloud - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="h3 mb-1">RoomSync Cloud</h1>
    <p class="text-muted">Status ruang pada <?= htmlspecialchars($date) ?> pukul <?= htmlspecialchars($time) ?> WIB</p>

    <form method="get" class="row g-2 mb-4">
        <div class="col-auto">
            <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <input type="time" name="time" value="<?= htmlspecialchars($time) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Cek Status</button>
        </div>
    </form>

    <div class="row g-3">
        <?php foreach ($statuses as $room => $status): ?>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Ruang <?= htmlspecialchars($room) ?></h2>
                        <span class="badge fs-6 <?= $kelas[$status] ?>"><?= htmlspecialchars($status) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
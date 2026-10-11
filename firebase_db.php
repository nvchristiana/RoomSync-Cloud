<?php
require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

function getDatabase()
{
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/firebase-credentials.json')
        ->withDatabaseUri('https://roomsync-cloud-default-rtdb.asia-southeast1.firebasedatabase.app');

    return $factory->createDatabase();
}
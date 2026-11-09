<?php

$settingsPath = storage_path('app/settings.json');
$storedSettings = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?: []) : [];

return array_merge([
    'election_name'         => 'Pemilihan Umum Digital',
    'institution'           => 'Instansi / Lembaga Pemilihan',
    'election_date'         => null,
    'start_time'            => null,
    'end_time'              => null,
    'status_pelaksanaan'    => 'buka',
    'enable_queue'          => true,
    'max_concurrent_voters' => 10,
    'booth_session_minutes' => 3,
    'timezone'              => 'Asia/Jakarta',
    'locale'                => 'id',
    'logo_instansi'         => null,
    'kiosk_mode'            => false,
    'kiosk_timeout_seconds' => 5,
    'enable_kotak_kosong'   => false,
    'enable_single_session' => true,
], $storedSettings);


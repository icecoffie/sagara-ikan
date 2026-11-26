<?php

if (!defined('SAGARA_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

return [

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'sagara_resto',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'base_url' => '',

    'app_name' => 'Sagara Resto',

    'timezone' => 'Asia/Jakarta',

    'upload_dir' => 'assets/uploads',

    // Pajak (PPN) & service charge dalam persen, diterapkan saat pembayaran.
    'tax_percent'     => 10,
    'service_percent' => 5,

    'debug' => false,
];

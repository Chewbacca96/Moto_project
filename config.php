<?php
return [
    'mark'  => [
        'BMW', 'HONDA', 'KAWASAKI', 'SUZUKI', 'YAMAHA', 'APRILIA', 'ARCTIC CAT', 'BOMBARDIER',
        'HARLEY DAVIDSON', 'BUELL', 'CAGIVA', 'DERBI', 'HUSABERG', 'GILERA', 'HUSQVARNA', 
        'HYOSUNG', 'KTM', 'MOTO GUZZI', 'MV Agusta', 'POLARIS', 'TRIUMPH', 'VICTORY'
    ],
    'type'  => [
        'Motorrad', 
        'ATV/Quad', 
        'Roller'
    ],
    'dbOpt' => [
        'host' => 'localhost',
        'db'   => 'motodb',
        'user' => 'root',
        'pass' => '#Data3456^'
    ],
    'error_log' => __DIR__.'/logs/php_errors.log'
];
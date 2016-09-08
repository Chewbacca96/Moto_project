<?php
	$checkManuf = ['BMW', 'HONDA', 'KAWASAKI', 'SUZUKI', 'YAMAHA', 'APRILIA', 'ARCTIC CAT', 'BOMBARDIER', 
		'HARLEY DAVIDSON', 'BUELL', 'CAGIVA', 'DERBI', 'HUSABERG', 'GILERA', 'HUSQVARNA', 'HYOSUNG', 'KTM', 
		'MOTO GUZZI', 'MV Agusta', 'POLARIS', 'TRIUMPH', 'VICTORY'];
	$checkBike = ['Motorrad', 'ATV/Quad', 'Roller'];

    $dbOpt = [
        'host' => 'localhost',
        'db'   => 'motodb',
        'user' => 'Chewy',
        'pass' => '123'
    ];

    return $config = [
        'manuf' => $checkManuf,
        'bike'  => $checkBike,
        'dbOpt' => $dbOpt
    ];
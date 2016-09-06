<?php
	$checkManuf = ['BMW', 'HONDA', 'KAWASAKI', 'SUZUKI', 'YAMAHA', 'APRILIA', 'ARCTIC CAT', 'BOMBARDIER', 
		'HARLEY DAVIDSON', 'BUELL', 'CAGIVA', 'DERBI', 'HUSABERG', 'GILERA', 'HUSQVARNA', 'HYOSUNG', 'KTM', 
		'MOTO GUZZI', 'MV Agusta', 'POLARIS', 'TRIUMPH', 'VICTORY'];
	$checkBike = ['Motorrad', 'ATV/Quad', 'Roller'];

	$host = 'localhost';
	$db = 'motodb';
	$user = 'Chewy';
	$pass = '123';

	$dsn = "mysql:host = $host; dbname = $db";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	];

	$pdo = new PDO($dsn, $user, $pass, $options);
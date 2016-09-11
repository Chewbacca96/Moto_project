<?php
	ini_set('max_execution_time', 0);
    require 'vendor\autoload.php';
	$config = require 'config.php';

    function connectToDB($dbOptions) {
        $host = $dbOptions['host'];
        $db   = $dbOptions['db'];
        $user = $dbOptions['user'];
        $pass = $dbOptions['pass'];

        $dsn = "mysql:host = $host; dbname = $db";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return new PDO($dsn, $user, $pass, $options);
    }

    function getMark($SiteStr) {
        $html = file_get_html($SiteStr);
        return $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');
    }

    function getBikeType($markValue) {
        $bikeTypeArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
        return json_decode($bikeTypeArr, true);
    }

    function getCapacity($markValue, $bikeType) {
        $capacityArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&
			bike-selection-fieldset[sortBySelect]=title&get=capacity");
        return json_decode($capacityArr, true);
    }

    function getModel($markValue, $bikeType, $capacitySize) {
        $data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&
			bike-selection-fieldset[capacity]=$capacitySize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
        return json_decode($data, true);
    }

    function modelToDB($pdo, $modelData, $mark, $bikeType) {
        $title = $modelData['title'];
        $manufStr = substr($title, 0, strripos($title, ', year'));
        $yearStart = substr($title, strripos($title, ', year') + 17, 4);

        if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
            $yearEnd = substr($title, strripos($title, ' - ') + 3, 4);
        } else {
            $yearEnd = null;
        }

        $frameStr = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);

        $stmt = $pdo->prepare('INSERT INTO motodb.bikedata (idmodel, mark, type, capacity, model, yearstart, yearend, frame) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$modelData['value'], $mark->innertext, $bikeType['value'], $modelData['capacity'],
            $manufStr, $yearStart, $yearEnd, $frameStr]);
    }

    $pdo = connectToDB($config['dbOpt']);
    $bdCheck = $pdo->prepare('SELECT idmodel FROM motodb.bikedata WHERE idmodel = ?');

    $markValue = getMark('https://www.louis.de/en');

	foreach($markValue as $markElem) {
		if (!in_array($markElem->innertext, $config['mark'])) {
            continue;
        }

        $bikeTypeArr = getBikeType($markElem->value);

        unset($bikeTypeArr['options'][0]);

        foreach ($bikeTypeArr['options'] as $bikeElem) {
            if (!in_array($bikeElem['value'], $config['type'])) {
                continue;
            }

            $capacityArr = getCapacity($markElem->value, $bikeElem['value']);

            unset($capacityArr['options'][0], $capacityArr['options'][1]);

            foreach ($capacityArr['options'] as $capacityElem) {
                $data = getModel($markElem->value, $bikeElem['value'], $capacityElem['value']);

                unset($data['options'][0]);

                foreach ($data['options'] as $dataElem) {
                    if ($bdCheck->execute([$dataElem['value']])) {
                        continue;
                    }

                    modelToDB($pdo, $dataElem, $markElem, $bikeElem);
                }
            }
        }
	}
	echo "I'm done.";
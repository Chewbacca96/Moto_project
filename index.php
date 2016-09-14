<?php
	ini_set('max_execution_time', 0);
    ini_set('log_errors', 'On');
    ini_set('error_log', 'php_errors.log');
    require 'vendor\autoload.php';
	$config = require 'config.php';

    class Mark {
        function __construct() {
        }

        function getFromURL($SiteStr) {
            $html = file_get_html($SiteStr);
            return $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');
        }

        function getFromDB($pdo, $mark) {
            $stmt = $pdo->prepare('SELECT id, value FROM motodb.t_mark WHERE value = ?');
            $stmt->execute([$mark]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function setToDB($pdo, $mark) {
            $stmt = $pdo->prepare('INSERT INTO motodb.t_mark (value) VALUE (?)');
            return $stmt->execute([$mark]);
        }
    }

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

    /*function getMark($SiteStr) {
        $html = file_get_html($SiteStr);
        return $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');
    }*/

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

    /*function checkMark($pdo, $mark) {
        $stmt = $pdo->prepare('SELECT id, value FROM motodb.t_mark WHERE value = ?');
        $stmt->execute([$mark]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }*/

    function checkType($pdo, $type) {
        $stmt = $pdo->prepare('SELECT id, value FROM motodb.t_type WHERE value = ?');
        $stmt->execute([$type]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*function markToDB($pdo, $mark) {
        $stmt = $pdo->prepare('INSERT INTO motodb.t_mark (value) VALUE (?)');
        return $stmt->execute([$mark]);
    }*/

    function typeToDB($pdo, $type) {
        $stmt = $pdo->prepare('INSERT INTO motodb.t_type (value) VALUE (?)');
        return $stmt->execute([$type]);
    }

    function modelToDB($pdo, $markid, $typeid, $modelData) {
        $title = $modelData['title'];
        $manufStr = substr($title, 0, strripos($title, ', year'));
        $yearStart = substr($title, strripos($title, ', year') + 17, 4);

        if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
            $yearEnd = substr($title, strripos($title, ' - ') + 3, 4);
        } else {
            $yearEnd = null;
        }

        $frameStr = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);

        $stmt = $pdo->prepare('INSERT INTO motodb.t_model (mark_id, type_id, code, model, capacity, year_start, year_end, frame) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$markid, $typeid, $modelData['value'], $manufStr, $modelData['capacity'], $yearStart, $yearEnd, $frameStr]);
    }

    /*function modelToDB($pdo, $modelData, $mark, $bikeType) {
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
    }*/

    $pdo = connectToDB($config['dbOpt']);
    //$bdCheck = $pdo->prepare('SELECT idmodel FROM motodb.bikedata WHERE idmodel = ?');
    $modelCheck = $pdo->prepare('SELECT code FROM motodb.t_model WHERE code = ?');
    $markCheck = [];
    $typeCheck = [];

    $markValue = getMark('https://www.louis.de/en');

	foreach($markValue as $markElem) {
		if (!in_array($markElem->innertext, $config['mark'])) {
            continue;
        }

        if (!in_array($markElem->innertext, $markCheck)) {
            if ($markElem->innertext != checkMark($pdo, $markElem->innertext)['value']) {
                markToDB($pdo, $markElem->innertext);
            }

            $markCheck[$markElem->innertext] = checkMark($pdo, $markElem->innertext)['id'];
        }

        $bikeTypeArr = getBikeType($markElem->value);

        unset($bikeTypeArr['options'][0]);

        foreach ($bikeTypeArr['options'] as $bikeElem) {
            if (!in_array($bikeElem['value'], $config['type'])) {
                continue;
            }

            if (!in_array($bikeElem['value'], $typeCheck)) {
                if ($bikeElem['value'] != checkType($pdo, $bikeElem['value'])['value']) {
                    typeToDB($pdo, $bikeElem['value']);
                }

                $typeCheck[$bikeElem['value']] = checkType($pdo, $bikeElem['value'])['id'];
            }

            $capacityArr = getCapacity($markElem->value, $bikeElem['value']);

            unset($capacityArr['options'][0], $capacityArr['options'][1]);

            foreach ($capacityArr['options'] as $capacityElem) {
                $data = getModel($markElem->value, $bikeElem['value'], $capacityElem['value']);

                unset($data['options'][0]);

                foreach ($data['options'] as $dataElem) {
                    $modelCheck->execute([$dataElem['value']]);
                    if ($dataElem['value'] == $modelCheck->fetchColumn()) {
                        continue;
                    }

                    modelToDB($pdo, $markCheck[$markElem->innertext], $typeCheck[$bikeElem['value']], $dataElem);
                    //modelToDB($pdo, $dataElem, $markElem, $bikeElem);
                }
            }
        }
	}
	echo "I'm done.";
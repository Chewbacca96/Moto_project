<?php
	ini_set('max_execution_time', 0);
	include 'PHP_Simple_HTML_DOM_Parser\simple_html_dom.php';
	require 'config.php';

    function ConnectToDB($dbOptions) {
        $host = $dbOptions['host'];
        $db   = $dbOptions['db'];
        $user = $dbOptions['user'];
        $pass = $dbOptions['pass'];

        $dsn = "mysql:host = $host; dbname = $db";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return $pdo = new PDO($dsn, $user, $pass, $options);
    }

    function GetMarkValues($SiteStr) {
        $html = file_get_html($SiteStr);
        return $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');
    }

    function GetBikeType($markValue) {
        $bikeTypeArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
        return json_decode($bikeTypeArr, true);
    }

    function GetCapacity($markValue, $bikeType) {
        $capacityArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&
			bike-selection-fieldset[sortBySelect]=title&get=capacity");
        return json_decode($capacityArr, true);
    }

    function GetModelData($markValue, $bikeType, $capacitySize) {
        $data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&
			bike-selection-fieldset[capacity]=$capacitySize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
        return json_decode($data, true);
    }

    function ModelDataToDB($pdo, $modelData, $mark, $bikeType) {
        $title = $modelData['title'];
        $manufStr = substr($title, 0, strripos($title, ', year'));
        $yearStart = substr($title, strripos($title, ', year') + 17, 4);

        if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
            $yearEnd = substr($title, strripos($title, ' - ') + 3, 4);
        } else {
            $yearEnd = null;
        }

        $frameStr = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);

        $stmt = $pdo->prepare('INSERT INTO motodb.bikedata (id, mark, type, capacity, model, yearstart, yearend, frame) 
								VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$modelData['value'], $mark->innertext, $bikeType['value'], $modelData['capacity'],
            $manufStr, $yearStart, $yearEnd, $frameStr]);
    }

    $pdo = ConnectToDB($config['dbOpt']);

	/*$html = file_get_html('https://www.louis.de/en');
	$markValue = $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');*/
    $markValue = GetMarkValues('https://www.louis.de/en');

	foreach($markValue as $markElem) {
		if (($markElem->value > 0) && in_array($markElem->innertext, $config['mark'])) {
			/*$bikeTypeArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
				bike-selection-fieldset[manufacturer]=$markElem->value&bike-selection-fieldset[sortBySelect]=title&get=biketype");
			$bikeTypeArr = json_decode($bikeTypeArr, true);*/
            $bikeTypeArr = GetBikeType($markElem->value);

			unset($bikeTypeArr['options'][0]);

			foreach ($bikeTypeArr['options'] as $bikeElem) {
				if (in_array($bikeElem['value'], $config['type'])) {
					/*$bikeType = $bikeElem['value'];
					$capacityArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
						bike-selection-fieldset[manufacturer]=$markElem->value&bike-selection-fieldset[biketype]=$bikeType&
						bike-selection-fieldset[sortBySelect]=title&get=capacity");
					$capacityArr = json_decode($capacityArr, true);*/
                    $capacityArr = GetCapacity($markElem->value, $bikeElem['value']);
					
					unset($capacityArr['options'][0]);
					unset($capacityArr['options'][1]);

					foreach ($capacityArr['options'] as $capacityElem) {
						/*$capacitySize = $capacityElem['value'];
						$data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
							bike-selection-fieldset[manufacturer]=$markElem->value&bike-selection-fieldset[biketype]=$bikeType&
							bike-selection-fieldset[capacity]=$capacitySize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
						$data = json_decode($data, true);*/
                        $data = GetModelData($markElem->value, $bikeElem['value'], $capacityElem['value']);

						unset($data['options'][0]);

						foreach ($data['options'] as $dataElem) {
                            ModelDataToDB($pdo, $dataElem, $markElem, $bikeElem);
						}
					}
				}
			}
		}
	}
	echo "I'm done.";
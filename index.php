<?php
	ini_set('max_execution_time', 0);
	include 'PHP_Simple_HTML_DOM_Parser\simple_html_dom.php';
	require 'config.php';

	$host = $config['dbOpt']['host'];
	$db   = $config['dbOpt']['db'];
	$user = $config['dbOpt']['user'];
	$pass = $config['dbOpt']['pass'];

	$dsn = "mysql:host = $host; dbname = $db";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	];

	$pdo = new PDO($dsn, $user, $pass, $options);

	$html = file_get_html('https://www.louis.de/en');
	$manufValue = $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');

	foreach($manufValue as $manufElem) {
		if (($manufElem->value > 0) && in_array($manufElem->innertext, $config['manuf'])) {
			//echo "$manufElem->innertext - ";

			$bikeTypeList = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
				bike-selection-fieldset[manufacturer]=$manufElem->value&bike-selection-fieldset[sortBySelect]=title&get=biketype");
			$bikeTypeList = json_decode($bikeTypeList, true);

			unset($bikeTypeList['options'][0]);

			foreach ($bikeTypeList['options'] as $bikeElem) {
				if (in_array($bikeElem['value'], $config['bike'])) {
					//echo $bikeElem['value'] . ' - ';

					$bikeType = $bikeElem['value'];
					$engineSizeList = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
						bike-selection-fieldset[manufacturer]=$manufElem->value&bike-selection-fieldset[biketype]=$bikeType&
						bike-selection-fieldset[sortBySelect]=title&get=capacity");
					$engineSizeList = json_decode($engineSizeList, true);
					
					unset($engineSizeList['options'][0]);
					unset($engineSizeList['options'][1]);

					foreach ($engineSizeList['options'] as $engineElem) {
						//echo $engineElem['value'] . ', ';

						$engineSize = $engineElem['value'];
						$data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
							bike-selection-fieldset[manufacturer]=$manufElem->value&bike-selection-fieldset[biketype]=$bikeType&
							bike-selection-fieldset[capacity]=$engineSize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
						$data = json_decode($data, true);

						//array_shift($data['options']);
						unset($data['options'][0]);

						foreach ($data['options'] as $dataElem) {
							//echo $data['options'][$k]['title'] . '<br>';
							$title = $dataElem['title'];

							$manufStr = substr($title, 0, strripos($title, ', year'));
							//echo $manufStr . '<br>';

							$yearStart = substr($title, strripos($title, ', year') + 17, 4);
							//echo $yearStart . '<br>';

							if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
								$yearEnd = substr($title, strripos($title, ' - ') + 3, 4);
							} else {
								$yearEnd = null;
							}
							//echo $yearEnd . '<br>';

							$ramaStr = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);
							//echo $ramaStr . '<br>';

							//echo "$manufStr, $yearStart, $yearEnd, $ramaStr <br>";

							$stmt = $pdo->prepare('INSERT INTO motodb.bikedata (id, mark, type, capacity, model, yearstart, yearend, frame) 
								VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
							$stmt->execute([$dataElem['value'], $manufElem->innertext, $bikeElem['value'], $dataElem['capacity'],
								$manufStr, $yearStart, $yearEnd, $ramaStr]);
						}
						//echo '<br>';
					}
					//echo '<br>';
				}
			}
			//echo "<br>";
		}
	}
	echo "I'm done.";
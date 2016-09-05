<?php
	ini_set('max_execution_time', 900);
	include 'PHP_Simple_HTML_DOM_Parser\simple_html_dom.php';

	$checkManuf = array('BMW', 'HONDA', 'KAWASAKI', 'SUZUKI', 'YAMAHA', 'APRILIA', 'ARCTIC CAT', 'BOMBARDIER', 'HARLEY DAVIDSON', 'BUELL', 'CAGIVA', 'DERBI', 'HUSABERG', 'GILERA', 'HUSQVARNA', 'HYOSUNG', 'KTM', 'MOTO GUZZI', 'MV Agusta', 'POLARIS', 'TRIUMPH', 'VICTORY');
	$checkBike = array('Motorrad', 'ATV/Quad', 'Roller');

	$html = file_get_html('https://www.louis.de/en');
	$manufValue = $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');

	foreach($manufValue as $element) {
		if (($element->value > 0) and in_array($element->innertext, $checkManuf)) {
			//echo "$element->innertext - ";

			$bikeTypeList = file_get_html("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$element->value&bike-selection-fieldset[sortBySelect]=title&get=biketype");
			$bikeTypeList = json_decode($bikeTypeList, true);

			$i = 1;
			while ($i < count($bikeTypeList['options'])) {
				if (in_array($bikeTypeList['options'][$i]['value'], $checkBike)) {
					//echo $bikeTypeList['options'][$i]['value'] . ' - ';

					$bikeType = $bikeTypeList['options'][$i]['value'];
					$engineSizeList = file_get_html("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$element->value&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[sortBySelect]=title&get=capacity");
					$engineSizeList = json_decode($engineSizeList, true);

					$j = 2;
					while ($j < count($engineSizeList['options'])) {
						//echo $engineSizeList['options'][$j]['value'] . ', ';

						$engineSize = $engineSizeList['options'][$j]['value'];
						$data = file_get_html("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$element->value&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[capacity]=$engineSize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
						$data = json_decode($data, true);

						$k = 1;
						while ($k < count($data['options'])) {
							//echo $data['options'][$k]['title'] . '<br>';

							$manufStr = substr($data['options'][$k]['title'], 0, strripos($data['options'][$k]['title'], ', year'));
							//echo $manufStr . '<br>';

							$yearStart = substr($data['options'][$k]['title'], strripos($data['options'][$k]['title'], ', year') + 17, 4);
							//echo $yearStart . '<br>';

							if (substr($data['options'][$k]['title'], strripos($data['options'][$k]['title'], ' - ') + 3, 1) == '1') {
								$yearEnd = substr($data['options'][$k]['title'], strripos($data['options'][$k]['title'], ' - ') + 3, 4);
							} else {
								$yearEnd = '-';
							}
							//echo $yearEnd . '<br>';

							$ramaStr = substr($data['options'][$k]['title'], strripos($data['options'][$k]['title'], '(') + 1, strripos($data['options'][$k]['title'], ')') - strripos($data['options'][$k]['title'], '(') - 1);
							//echo $ramaStr . '<br>';

							//echo "$manufStr, $yearStart, $yearEnd, $ramaStr <br>";
							$k++;
						}

						//echo '<br>';
						$j++;
					}

					//echo '<br>';
				}
				$i++;
			}
			//echo "<br>";
		}
	}
?>
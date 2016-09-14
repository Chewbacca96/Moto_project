<?php
	ini_set('max_execution_time', 0);
    ini_set('log_errors', 'On');
    ini_set('error_log', 'php_errors.log');
    require 'vendor\autoload.php';
    require 'models\Mark.php';
    require 'models\Type.php';
    require 'models\Model.php';
	$config = require 'config.php';

    use MarkSpace\Mark as Mark;
    use TypeSapce\Type as Type;
    use ModelSpace\Model as Model;

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

    function getCapacity($markValue, $bikeType) {
        $capacityArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?
			bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&
			bike-selection-fieldset[sortBySelect]=title&get=capacity");
        return json_decode($capacityArr, true);
    }

    $Marks = new Mark($config);
    $Types = new Type($config);
    $Models = new Model($config);

    $markValue = $Marks->getFromURL();

	foreach($markValue as $markElem) {
		if (!in_array($markElem->innertext, $config['mark'])) {
            continue;
        }

        if (!$Marks->getFromDB($markElem->innertext)) {
            $Marks->setToDB($markElem->innertext);
        }

        $bikeTypeArr = $Types->getFromURL($markElem->value);

        unset($bikeTypeArr['options'][0]);

        foreach ($bikeTypeArr['options'] as $bikeElem) {
            if (!in_array($bikeElem['value'], $config['type'])) {
                continue;
            }

            if (!$Types->getFromDB($bikeElem['value'])) {
                $Types->setToDB($bikeElem['value']);
            }

            $capacityArr = getCapacity($markElem->value, $bikeElem['value']);

            unset($capacityArr['options'][0], $capacityArr['options'][1]);

            foreach ($capacityArr['options'] as $capacityElem) {
                $data = $Models->getFromURL($markElem->value, $bikeElem['value'], $capacityElem['value']);

                unset($data['options'][0]);

                foreach ($data['options'] as $dataElem) {
                    if ($Models->getFromDB($dataElem['value'])) {
                        continue;
                    }

                    $Models->setToDB($Marks->getFromDB($markElem->innertext), $Types->getFromDB($bikeElem['value']), $dataElem);
                }
            }
        }
	}
	echo "I'm done.";
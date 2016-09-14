<?php
	ini_set('max_execution_time', 0);
    ini_set('log_errors', 'On');
    ini_set('error_log', 'php_errors.log');
    require 'vendor\autoload.php';
    require 'models\Mark.php';
    require 'models\Type.php';
    require 'models\Capacity.php';
    require 'models\Model.php';
	$config = require 'config.php';

    use MarkSpace\Mark as Mark;
    use TypeSapce\Type as Type;
    use CapacitySpace\Capacity as Capacity;
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

    $Marks = new Mark($config);
    $Types = new Type($config);
    $Size = new Capacity;
    $Models = new Model($config);

    $markValues = $Marks->getFromURL();

	foreach($markValues as $markValue) {
		if (!in_array($markValue->innertext, $config['mark'])) {
            continue;
        }

        $markID = $Marks->getFromDB($markValue->innertext);
        if (!$markID) {
            $markID = $Marks->setToDB($markValue->innertext);
        }

        $bikeTypes = $Types->getFromURL($markValue->value);

        foreach ($bikeTypes as $bikeType) {
            if (!in_array($bikeType['value'], $config['type'])) {
                continue;
            }

            $typeID = $Types->getFromDB($bikeType['value']);
            if (!$typeID) {
                $typeID = $Types->setToDB($bikeType['value']);
            }

            $capacityValues = $Size->getFromURL($markValue->value, $bikeType['value']);

            foreach ($capacityValues as $capacityValue) {
                $data = $Models->getFromURL($markValue->value, $bikeType['value'], $capacityValue['value']);

                foreach ($data as $dataElem) {
                    if ($Models->getFromDB($dataElem['value'])) {
                        continue;
                    }

                    $Models->setToDB($markID, $typeID, $dataElem);
                }
            }
        }
	}
	echo "I'm done.";
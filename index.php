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

	foreach($markValues as $markElem) {
		if (!in_array($markElem->innertext, $config['mark'])) {
            continue;
        }

        $markID = $Marks->getFromDB($markElem->innertext);
        if (!$markID) {
            $markID = $Marks->setToDB($markElem->innertext);
        }

        $bikeTypes = $Types->getFromURL($markElem->value);

        foreach ($bikeTypes as $bikeElem) {
            if (!in_array($bikeElem['value'], $config['type'])) {
                continue;
            }

            $typeID = $Types->getFromDB($bikeElem['value']);
            if (!$typeID) {
                $typeID = $Types->setToDB($bikeElem['value']);
            }

            $capacity = $Size->getFromURL($markElem->value, $bikeElem['value']);

            foreach ($capacity as $capacityElem) {
                $data = $Models->getFromURL($markElem->value, $bikeElem['value'], $capacityElem['value']);

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
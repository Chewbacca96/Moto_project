<?php
	ini_set('max_execution_time', 0);
    ini_set('log_errors', 'On');
    ini_set('error_log', 'php_errors.log');
    require 'vendor\autoload.php';
    require 'models\Mark.php';
    require 'models\Type.php';
    require 'models\Model.php';
    require 'models\DB.php';
	$config = require 'config.php';

    use MarkSpace\Mark as Mark;
    use TypeSapce\Type as Type;
    use Motopitlane\Models\Capacity as Capacity;
    use ModelSpace\Model as Model;
    use DBSapce\DB as DB;

    /*function connectToDB($dbOptions) {
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
    }*/

    $Marks = new Mark($config);
    $Types = new Type($config);
    $Size = new Capacity;
    $Models = new Model($config);

    $markValues = $Marks->getFromURL();

	foreach($markValues as $markValue) {
		if (!in_array($markValue['value'], $config['mark'])) {
            continue;
        }

        $markID = $Marks->getFromDB($markValue['value']);

        $bikeTypes = $Types->getFromURL($markValue['code']);

        foreach ($bikeTypes as $bikeType) {
            if (!in_array($bikeType['value'], $config['type'])) {
                continue;
            }

            $typeID = $Types->getFromDB($bikeType['value']);

            $capacityValues = $Size->getFromURL($markValue['code'], $bikeType['value']);

            foreach ($capacityValues as $capacityValue) {
                $modelsData = $Models->getFromURL($markValue['code'], $bikeType['value'], $capacityValue['value']);

                foreach ($modelsData as $modelData) {
                    /*if (!$Models->getFromDB($modelData['value'])) {
                        $Models->setToDB($markID, $typeID, $modelData);
                    }*/
                    $Models->getFromDB($modelData, $markID, $typeID);
                }
            }
        }
	}
	echo "I'm done.";

<?php
namespace Motopitlane;
	ini_set('max_execution_time', 0);
    ini_set('log_errors', 'On');
    ini_set('error_log', 'php_errors.log');
    require 'vendor\autoload.php';
	$config = require 'config.php';

    use Motopitlane\Mark as Mark;
    use Motopitlane\Type as Type;
    use Motopitlane\Capacity as Capacity;
    use Motopitlane\Model as Model;

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
                    $Models->getFromDB($modelData, $markID, $typeID);
                }
            }
        }
	}
	echo "I'm done.";

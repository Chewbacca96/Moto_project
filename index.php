<?php
namespace Motopitlane;

use Motopitlane\Mark as Mark;
use Motopitlane\Type as Type;
use Motopitlane\Capacity as Capacity;
use Motopitlane\Model as Model;

require 'vendor/autoload.php';
$config = require (isset($argv[1])) ? $argv[1] : 'config.php';

ini_set('max_execution_time', 0);
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');
date_default_timezone_set('Europe/Moscow');

$Marks  = new Mark($config['dbOpt']);
$Types  = new Type($config['dbOpt']);
$Size   = new Capacity;
$Models = new Model($config['dbOpt']);

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

        $capacityValues = $Size->getFromURL(
            $markValue['code'], 
            $bikeType['value']
        );

        foreach ($capacityValues as $capacityValue) {
            $modelsData = $Models->getFromURL(
                $markValue['code'], 
                $bikeType['value'], 
                $capacityValue['value']
            );

            foreach ($modelsData as $modelData) {
                $Models->getFromDB($modelData, $markID, $typeID);
            }
        }
    }
}
echo "\nI'm done.\n";

<?php
namespace Moto_project;

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Moto_project\models\Mark as Mark;
use Moto_project\models\Type as Type;
use Moto_project\models\Capacity as Capacity;
use Moto_project\models\Model as Model;

require 'vendor/autoload.php';
$config = require (isset($argv[1])) ? $argv[1] : 'config.php';

ini_set('log_errors', 'On');
ini_set('error_log', $config['error_log']);
ini_set('max_execution_time', 0);
date_default_timezone_set('Europe/Moscow');

$start = microtime(true);

$syslog = new Logger('Syslog');
$syslog->pushHandler(new SyslogHandler('motoCrawler'));

$syslog->info('The script '.$argv[0].' started.');

$Marks  = new Mark(
    $config['dbOpt']['host'],
    $config['dbOpt']['db'],
    $config['dbOpt']['user'],
    $config['dbOpt']['pass']
);
$Types  = new Type(
    $config['dbOpt']['host'],
    $config['dbOpt']['db'],
    $config['dbOpt']['user'],
    $config['dbOpt']['pass']
);
$Size   = new Capacity;
$Models = new Model(
    $config['dbOpt']['host'],
    $config['dbOpt']['db'],
    $config['dbOpt']['user'],
    $config['dbOpt']['pass']
);

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

$syslog->info('Script finished in '.(microtime(true) - $start).' sec.');

echo "\nI'm done.\n";
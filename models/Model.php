<?php
namespace ModelSpace;

    class Model {
        static private $pdo;
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = connectToDB($config['dbOpt']);
            }
        }
    
        public function getFromURL($markValue, $bikeType, $capacityValue) {
            $data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[capacity]=$capacityValue&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
            $data = json_decode($data, true);
            unset($data['options'][0]);
            return $data['options'];
        }
    
        public function getFromDB($code) {
            $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_model WHERE code = ?');
            $stmt->execute([$code]);
            return $stmt->fetchColumn();
        }

        public function parseModel($modelData) {
            $title = $modelData['title'];
            $data['modelStr'] = substr($title, 0, strripos($title, ', year'));
            $data['yearStart'] = substr($title, strripos($title, ', year') + 17, 4);

            if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
                $data['yearEnd'] = substr($title, strripos($title, ' - ') + 3, 4);
            } else {
                $data['yearEnd'] = null;
            }

            $data['frameStr'] = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);
            return $data;
        }

        public function setToDB($markID, $typeID, $modelData) {
            $data = $this->parseModel($modelData);
    
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_model (mark_id, type_id, code, model, capacity, year_start, year_end, frame) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$markID, $typeID, $modelData['value'], $data['modelStr'], $modelData['capacity'], $data['yearStart'], $data['yearEnd'], $data['frameStr']]);
            return self::$pdo->lastInsertId();
        }
    }
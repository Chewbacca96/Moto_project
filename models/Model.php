<?php
namespace ModelSpace;

    class Model {
        static private $pdo;
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = connectToDB($config['dbOpt']);
            }
        }
    
        public function getFromURL($markValue, $bikeType, $capacitySize) {
            $data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[capacity]=$capacitySize&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
            return json_decode($data, true);
        }
    
        public function getFromDB($code) {
            $stmt = self::$pdo->prepare('SELECT code FROM motodb.t_model WHERE code = ?');
            $stmt->execute([$code]);
            return $stmt->fetchColumn();
        }
    
        public function setToDB($markid, $typeid, $modelData) {
            $title = $modelData['title'];
            $manufStr = substr($title, 0, strripos($title, ', year'));
            $yearStart = substr($title, strripos($title, ', year') + 17, 4);
    
            if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
                $yearEnd = substr($title, strripos($title, ' - ') + 3, 4);
            } else {
                $yearEnd = null;
            }
    
            $frameStr = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);
    
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_model (mark_id, type_id, code, model, capacity, year_start, year_end, frame) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$markid, $typeid, $modelData['value'], $manufStr, $modelData['capacity'], $yearStart, $yearEnd, $frameStr]);
            return self::$pdo->lastInsertId();
        }
    }
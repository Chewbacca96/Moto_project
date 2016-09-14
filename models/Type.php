<?php
namespace TypeSapce;

    class Type {
        static private $pdo;
        static private $typeFromDB = [];
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = connectToDB($config['dbOpt']);
            }
        }
    
        public function getFromURL($markValue) {
            $bikeTypes = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
            $bikeTypes = json_decode($bikeTypes, true);
            unset($bikeTypes['options'][0]);
            return $bikeTypes['options'];
        }
    
        public function getFromDB($type) {
            if (!in_array($type, self::$typeFromDB)) {
                $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_type WHERE value = ?');
                $stmt->execute([$type]);
                self::$typeFromDB[$type] = $stmt->fetchColumn();
            }
            return self::$typeFromDB[$type];
        }
    
        public function setToDB($type) {
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_type (value) VALUE (?)');
            $stmt->execute([$type]);
            return self::$pdo->lastInsertId();
        }
    }
<?php
namespace TypeSapce;

    class Type {
        static private $pdo;
        static private $typeFromDB;
    
        public function __construct($config) {
            self::$typeFromDB = [];
    
            if(!self::$pdo) {
                self::$pdo = connectToDB($config['dbOpt']);
            }
        }
    
        public function getFromURL($markValue) {
            $bikeTypeArr = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
            return json_decode($bikeTypeArr, true);
        }
    
        public function getFromDB($type) {
            if (!in_array($type, self::$typeFromDB)) {
                $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_type WHERE value = ?');
                $stmt->execute([$type]);
                return self::$typeFromDB[$type] = $stmt->fetchColumn();
            } else { return self::$typeFromDB[$type]; }
        }
    
        public function setToDB($type) {
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_type (value) VALUE (?)');
            $stmt->execute([$type]);
            return self::$pdo->lastInsertId();
        }
    }
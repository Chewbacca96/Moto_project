<?php
namespace TypeSapce;
    
    use DBSapce\DB as DB;

    class Type {
        static private $pdo;
        static private $typeFromDB = [];
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = DB::connectToDB($config['dbOpt']);
            }
        }

        public function setToDB($type) {
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_type (value) VALUE (?)');
            $stmt->execute([$type]);
            return self::$pdo->lastInsertId();
        }
    
        public function getFromDB($type, $append = true) {
            if (!array_key_exists($type, self::$typeFromDB)) {
                $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_type WHERE value = ?');
                $stmt->execute([$type]);
                $stmt = $stmt->fetchColumn();
                if($stmt) {
                    self::$typeFromDB[$type] = $stmt;
                } elseif ($append) {
                    self::$typeFromDB[$type] = $this->setToDB($type);
                } else {
                    self::$typeFromDB[$type] = null;
                }
            }
            return self::$typeFromDB[$type];
        }

        public function getFromURL($markValue) {
            $bikeTypes = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
            $bikeTypes = json_decode($bikeTypes, true);
            unset($bikeTypes['options'][0]);
            return $bikeTypes['options'];
        }
    }
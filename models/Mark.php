<?php
namespace Motopitlane\models;

    class Mark {
        const SITE = 'https://www.louis.de/en';
        static private $pdo;
        static private $markFromDB = [];
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = DB::connectToDB($config['dbOpt']);
            }
        }
    
        public function setToDB($mark) {
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_mark (value) VALUE (?)');
            $stmt->execute([$mark]);
            return self::$pdo->lastInsertId();
        }

        public function getFromDB($mark, $append = true) {
            if (!array_key_exists($mark, self::$markFromDB)) {
                $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_mark WHERE value = ?');
                $stmt->execute([$mark]);
                $stmt = $stmt->fetchColumn();
                if($stmt) {
                    self::$markFromDB[$mark] = $stmt;
                } elseif ($append) {
                    self::$markFromDB[$mark] = $this->setToDB($mark);
                } else {
                    self::$markFromDB[$mark] = null;
                }
            }
            return self::$markFromDB[$mark];
        }

        public function getFromURL() {
            $html = file_get_html(self::SITE);
            $html = $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');

            $i = 0;
            foreach ($html as $markValue) {
                $markValues[$i]['value'] = $markValue->innertext;
                $markValues[$i]['code'] = $markValue->value;
                $i++;
            }
            return $markValues;
        }
    }
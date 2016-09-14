<?php
namespace MarkSpace;

    class Mark {
        const SITE = 'https://www.louis.de/en';
        static private $pdo;
        static private $markFromDB = [];
    
        public function __construct($config) {
            if(!self::$pdo) {
                self::$pdo = connectToDB($config['dbOpt']);
            }
        }
    
        public function getFromDB($mark) {
            if (!in_array($mark, self::$markFromDB)) {
                $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_mark WHERE value = ?');
                $stmt->execute([$mark]);
                self::$markFromDB[$mark] = $stmt->fetchColumn();
            }
            return self::$markFromDB[$mark];
        }
    
        public function setToDB($mark) {
            $stmt = self::$pdo->prepare('INSERT INTO motodb.t_mark (value) VALUE (?)');
            $stmt->execute([$mark]);
            return self::$pdo->lastInsertId();
        }

        public function getFromURL() {
            $html = file_get_html(self::SITE);
            return $html->find('select[id=bikedb-flyout-manufacturer]', 0)->find('option');
        }
    }
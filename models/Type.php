<?php
namespace Motopitlane;

class Type 
{
    private static $pdo;
    private static $typeFromDB = [];

    /**
     * Type конструктор
     *
     * @param array $dbOptions массив опций для подключения к БД
     */
    public function __construct($dbOptions) {
        if(!self::$pdo) {
            self::$pdo = DB::connectToDB($dbOptions);
        }
    }

    /**
     * Функция добавляет запись в БД
     *
     * @param string $type название типа
     *
     * @return int id добавленной записи
     */
    public function setToDB($type) 
    {
        $stmt = self::$pdo->prepare('INSERT INTO motodb.t_type (value) VALUE (?)');
        $stmt->execute([$type]);
        return self::$pdo->lastInsertId();
    }

    /**
     * Функция возвращает id записи из БД
     *
     * @param string $type название типа
     * @param boolean $append
     *
     * @return id записи
     */
    public function getFromDB($type, $append = true) 
    {
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

    /**
     * Функция парсит типы моделей
     *
     * @param int $markValue код марки моделей
     *
     * @return array массив с названиями типов
     */
    public function getFromURL($markValue) 
    {
        $bikeTypes = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[sortBySelect]=title&get=biketype");
        $bikeTypes = json_decode($bikeTypes, true);
        unset($bikeTypes['options'][0]);
        return $bikeTypes['options'];
    }
}
<?php
namespace Moto_project\models;

class Type 
{
    private static $pdo;
    private static $typeFromDB = [];

    /**
     * Type конструктор
     *
     * @param string $host доменное имя сервера базы данных
     * @param string $db имя базы данных
     * @param string $user логи пользователя базы данных
     * @param string $pass пароль пользователя базы данных
     */
    public function __construct($host, $db, $user, $pass) {
        if(!self::$pdo) {
            self::$pdo = DB::connectToDB($host, $db, $user, $pass);
        }
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
        $bikeTypes = file_get_contents('https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]='.$markValue.'&bike-selection-fieldset[sortBySelect]=title&get=biketype');
        $bikeTypes = json_decode($bikeTypes, true);
        unset($bikeTypes['options'][0]);

        return $bikeTypes['options'];
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
}
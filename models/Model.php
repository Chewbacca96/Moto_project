<?php
namespace Motopitlane;

class Model 
{
    private static $pdo;
    private static $modelFromDB = [];

    /**
     * Model конструктор
     *
     * @param array $dbOptions массив опций для подключения к БД
     */
    public function __construct($dbOptions) 
    {
        if(!self::$pdo) {
            self::$pdo = DB::connectToDB($dbOptions);
        }
    }

    /**
     * Функция разбивает строчку с информацией о модели на переменные
     *
     * @param string $modelData строка информации о модели
     *
     * @return array массив с информацией разбитой по категориям
     */
    public function parseModel($modelData) 
    {
        $title = $modelData['title'];
        $data['modelStr'] = substr($title, 0, strripos($title, ', year'));
        if (substr($title, strripos($title, ', year') + 17, 1) == '1') {
            $data['yearStart'] = substr($title, strripos($title, ', year') + 17, 4);
        } else {
            $data['yearStart'] = null;
        }

        if (substr($title, strripos($title, ' - ') + 3, 1) == '1') {
            $data['yearEnd'] = substr($title, strripos($title, ' - ') + 3, 4);
        } else {
            $data['yearEnd'] = null;
        }

        $data['frameStr'] = substr($title, strripos($title, '(') + 1, strripos($title, ')') - strripos($title, '(') - 1);
        return $data;
    }

    /**
     * Функция добавления информации о модели в БД
     *
     * @param int $markID id марки модели
     * @param int $typeID id типа модели
     * @param array $modelData массив с информацией разбитой по категориям
     *
     * @return id добавленной в БД записи
     */
    public function setToDB($markID, $typeID, $modelData) 
    {
        $data = $this->parseModel($modelData);

        $stmt = self::$pdo->prepare('INSERT INTO motodb.t_model (mark_id, type_id, code, model, capacity, year_start, year_end, frame) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$markID, $typeID, $modelData['value'], $data['modelStr'], $modelData['capacity'], $data['yearStart'], $data['yearEnd'], $data['frameStr']]);
        return self::$pdo->lastInsertId();
    }

    /**
     * Функция возвращает id записи из БД
     *
     * @param array $modelData массив с информацией разбитой по категориям
     * @param int $markID марки модели
     * @param int $typeID типа модели
     * @param boolean $append
     *
     * @return int id записи
     */
    public function getFromDB($modelData, $markID, $typeID, $append = true) 
    {
        if (!array_key_exists($modelData['value'], self::$modelFromDB)) {
            $stmt = self::$pdo->prepare('SELECT id FROM motodb.t_model WHERE code = ?');
            $stmt->execute([$modelData['value']]);
            $stmt = $stmt->fetchColumn();
            if ($stmt) {
                self::$modelFromDB[$modelData['value']] = $stmt;
            } elseif ($append) {
                self::$modelFromDB[$modelData['value']] = $this->setToDB($markID, $typeID, $modelData);
            } else {
                self::$modelFromDB[$modelData['value']] = null;
            }
        }
        return self::$modelFromDB[$modelData['value']];
    }

    /**
     * Функция парсит полную информацию о модели
     * 
     * @param int $markValue код марки модели
     * @param string $bikeType название типа модели
     * @param int $capacityValue код объема модели
     *
     * @return string строка информации о модели
     */
    public function getFromURL($markValue, $bikeType, $capacityValue) 
    {
        $data = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[capacity]=$capacityValue&bike-selection-fieldset[sortBySelect]=title&sortby=title&get=bikes");
        $data = json_decode($data, true);
        unset($data['options'][0]);
        return $data['options'];
    }
}
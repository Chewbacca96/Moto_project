<?php
namespace Motopitlane;

class Mark 
{
    const SITE = 'https://www.louis.de/en';
    private static $pdo;
    private static $markFromDB = [];

    /**
     * Mark конструктор
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
     * Функция добавления марки в БД
     *
     * @param string $mark название марки
     *
     * @return int id добавленной в БД записи
     */
    public function setToDB($mark) 
    {
        $stmt = self::$pdo->prepare('INSERT INTO motodb.t_mark (value) VALUE (?)');
        $stmt->execute([$mark]);
        return self::$pdo->lastInsertId();
    }

    /**
     * Функция возвращает id марки из БД
     *
     * @param string $mark название марки
     * @param boolean $append
     *
     * @return int id записи в БД
     */
    public function getFromDB($mark, $append = true) 
    {
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

    /**
     * Функция парсит название и коды марок с сайта
     *
     * @return array массив названий и кодов марок
     */
    public function getFromURL() 
    {
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
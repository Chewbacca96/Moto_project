<?php
namespace Motopitlane;

use PDO;

class DB 
{
    private static $pdo;

    /**
     * Функция для подключения к базе данных
     *
     * @param array $dbOptions массив с опциями для подключения к БД
     *
     * @return object объект, представляющий соединение с сервером базы данных
     */
    public static function connectToDB($dbOptions) 
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $host = $dbOptions['host'];
        $db   = $dbOptions['db'];
        $user = $dbOptions['user'];
        $pass = $dbOptions['pass'];

        $dsn = "mysql:host = $host; dbname = $db";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return self::$pdo = new PDO($dsn, $user, $pass, $options);
    }
}    
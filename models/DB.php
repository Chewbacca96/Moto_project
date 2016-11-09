<?php
namespace Moto_project\models;

use PDO;

class DB 
{
    private static $pdo;

    /**
     * Функция для подключения к базе данных
     *
     * @param string $host доменное имя сервера базы данных
     * @param string $db имя базы данных
     * @param string $user логи пользователя базы данных
     * @param string $pass пароль пользователя базы данных
     *
     * @return object объект, представляющий соединение с сервером базы данных
     */
    public static function connectToDB($host, $db, $user, $pass) 
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $host = $host;
        $db   = $db;
        $user = $user;
        $pass = $pass;

        $dsn = 'mysql:host = '.$host.'; dbname = '.$db;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return self::$pdo = new PDO($dsn, $user, $pass, $options);
    }
}    
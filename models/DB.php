<?php
namespace DBSapce;

    class DB {
        static public function connectToDB($dbOptions) {
            $host = $dbOptions['host'];
            $db   = $dbOptions['db'];
            $user = $dbOptions['user'];
            $pass = $dbOptions['pass'];
    
            $dsn = "mysql:host = $host; dbname = $db";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
    
            return new PDO($dsn, $user, $pass, $options);
        }
    }    
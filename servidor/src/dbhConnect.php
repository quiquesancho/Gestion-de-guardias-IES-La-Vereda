<?php

class ConexionDB {
    
    const DSN = 'mysql:dbname=guardias;host=localhost';
    const USER = 'root';
    const PASSWORD = '';

    const OPTIONS = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_PERSISTENT => true
    );

    public static function conectar() : PDO {
        $dbh = new PDO(self::DSN, self::USER, self::PASSWORD, self::OPTIONS);

        return $dbh;
    }
}
?>
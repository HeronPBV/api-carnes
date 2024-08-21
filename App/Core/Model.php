<?php

namespace App\Core;

class Model {

    private static $conexao;

    public static function getConn(){

        if(!isset(self::$conexao)){
            self::$conexao = new \PDO(DBDRIVE .":host=". DBHOST .";port=". DBPORT .";dbname=". DBNAME .";", DBUSER, DBPASS);
        }

        return self::$conexao;
    }

}

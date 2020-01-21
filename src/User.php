<?php


namespace App;


class User extends Magic
{

    public static function __callStatic($name, $params)
    {
        parent::__callStatic($name, $params);
    }


}
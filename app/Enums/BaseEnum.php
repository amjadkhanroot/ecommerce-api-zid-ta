<?php

namespace App\Enums;


class BaseEnum
{
    private static $values;

    public static function get($key)
    {
        return static::$values[$key];
    }

    public static function getKey($value)
    {
        $keys = array_flip(static::$values);

        return $keys[$value];
    }

    public static function list()
    {
        return static::$values;
    }

    public static function exist($inputKey): bool
    {
        foreach (static::$values as $key => $value) {
            if ($inputKey == $key)
                return true;
        }

        return false;
    }

    public static function getKeyObject($inputValue)
    {
        foreach (static::$values as $key => $value) {
            if ($inputValue == $value['value'])
                return $key;
        }

        return null;
    }
}

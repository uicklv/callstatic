<?php

namespace App;


abstract class Magic
{
    public static function __callStatic($name, $params)
    {
        $table = self::pluralize(2, strtolower(end(explode('\\', static::class))));
        $pdo = new \PDO('mysql:host=mysql;dbname=docker', 'root', 'password');
        $sql = 'SELECT * FROM ' . $table . ' WHERE ';
        $name = explode('And', str_replace('find', '', $name));
        $values_bind = array();
        foreach ($name as $key => $value) {
            if (strpos($value, 'Between') !== false) {
                $value = mb_strtolower(str_replace('Between', '', $value));
                $value_date = substr_replace($value, "_", 7, 0) . ' BETWEEN :start_date AND :end_date ';
                array_push($values_bind, ':start_date', ':end_date');
            } elseif (strpos($value, 'By') !== false) {
                $value = mb_strtolower(str_replace('By', '', $value));
                $values_by[] = $value . ' = :' . $value;
                array_push($values_bind, ':' . $value);

            } elseif (strpos($value, 'In') !== false) {
                $value = mb_strtolower(str_replace('In', '', $value));
                $value_in = ' AND ' . $value . ' IN ' . ' (:' . $value . '_0, :' . $value . '_1)';
                array_push($values_bind, ':' . $value . '_0', ':' . $value . '_1');
                $last_param = array_pop($params);
                foreach ($last_param as $key => $value) {
                    array_push($params, $value);
                }
            }
        }
        if (!$values_by && $value_date) {
            $sql .= $value_date;
        } elseif ($values_by && !$value_date) {
            $sql .= implode(' AND ', $values_by);
        } elseif ($value_date && $values_by) {
            $sql .= $value_date . ' AND ' . implode(' AND ', $values_by);
        }
        $sql .= $value_in;
        var_dump($sql);
        $stmt = $pdo->prepare($sql);
        $leng = count($params);
        for ($i = 0; $i < $leng; $i++) {
            $stmt->bindValue($values_bind[$i], $params[$i]);
        }
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            throw new \Exception('Record does not exist.');
        }
        return $data;

    }

    public static function pluralize($quantity, $singular, $plural = null)
    {
        if ($quantity == 1 || !strlen($singular)) return $singular;
        if ($plural !== null) return $plural;

        $last_letter = strtolower($singular[strlen($singular) - 1]);
        switch ($last_letter) {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }
}
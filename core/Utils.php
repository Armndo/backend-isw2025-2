<?php
namespace Core;

use PDO;
use PDOException;

class Utils {
  public static function where($condition, $toString = false): array | string {
    $field = null;
    $operator = null;
    $value = null;

    if (is_string($condition)) {
      return $condition;
    }

    if (sizeof($condition) === 2) {
      [$field, $value] = $condition;
      $operator = "=";
    } else {
      [$field, $operator, $value] = $condition;
    }

    if (is_string($value) && !$toString) {
      $value = "'$value'";
    } else if ($value === null) {
      $value = "NULL";
    }

    return $toString ? implode(".", array_map(function($item) {
        return "\"$item\"";
      }, explode(".", $field))) . " $operator $value" : [
      "field" => $field,
      "operator" => $operator,
      "value" => $value,
    ];
  }

  public static function selects($fields) {
    $selects = [];

    foreach ($fields as $field) {
      $selects[] = $field !== "*" ? implode(".", array_map(function($item) {
        return $item === "*" ? $item : "\"$item\"";
      }, explode(".", $field))) : $field;
    }

    return implode(", ", $selects);
  }

  public static function wheres($conditions, $toString = false): array | string {
    if (sizeof($conditions) === 0) {
      return $toString ? "" : [];
    }

    $wheres = [];

    foreach($conditions as $condition) {
      if (is_string($condition)) {
        $wheres[] = static::where($condition);
      } else {
        $wheres[] = static::where($toString ? array_values($condition) : $condition, $toString);
      }
    }

    return $toString ? (" WHERE " . implode(" AND ", $wheres)) : $wheres;
  }

  public static function fields($fields, $appends, $identifier): string {
    $aux = [];

    foreach(array_keys($fields) as $field) {
      if (in_array($field, $appends)) {
        continue;
      }

      $aux[] = "\"$field\"";
    }

    return implode(", ", $aux);
  }

  public static function values($fields, $appends, $update = false, $identifier = null): string {
    $aux = [];

    foreach($fields as $field => $value) {
      if (is_null($value) || ($field === $identifier && !is_string($field)) || in_array($field, $appends)) {
        continue;
      } else if (is_string($value)) {
        $value = "'$value'";
      }

      $aux[] = !$update ? $value : "\"$field\" = $value";
    }

    return implode(", ", $aux);
  }

  public static function orders($orders = []) {
    if (sizeof($orders) === 0) {
      return "";
    }

    return " ORDER BY " . implode(", ", array_map(function ($order) {
      [$field, $direction] = $order;
      return "\"$field\" $direction";
    }, $orders));
  }
}
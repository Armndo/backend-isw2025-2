<?php
include_once("Connection.php");

class Utils {
  public static function where($condition, $toString = false): mixed {
    $field = null;
    $operator = null;
    $value = null;

    if (sizeof($condition) === 2) {
      [$field, $value] = $condition;
      $operator = "=";
    } else {
      [$field, $operator, $value] = $condition;
    }

    if (gettype($value) === "string" && !$toString) {
      $value = "'$value'";
    } else if ($value === null) {
      $value = "NULL";
    }

    return $toString ? "$field $operator $value" : [
      "field" => $field,
      "operator" => $operator,
      "value" => $value,
    ];
  }

  public static function wheres($conditions, $toString = false): mixed {
    if (sizeof($conditions) === 0) {
      return $toString ? "" : [];
    }

    $wheres = [];

    foreach($conditions as $condition) {
      $wheres[] = static::where($toString ? array_values($condition) : $condition, $toString);
    }

    return $toString ? (" where " . implode(" and ", $wheres)) : $wheres;
  }

  public static function runQuery($fields, $table, $where) {
    try {
      $conn = (new Connection())->getConnection();
      $sql = "SELECT $fields FROM $table$where";
      $query = $conn->query($sql);

      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
    } finally {
      $conn = null;
    }
  }
}
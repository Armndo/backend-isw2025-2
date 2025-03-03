<?php
class Utils {
  public static function where($condition, $flag = true): string {
    $where = $flag ? " where " : "";

    if (sizeof($condition) === 2) {
      [$field, $value] = $condition;

      if (gettype($value) === "string") {
        $value = "'$value'";
      }

      $where .= "$field = $value";
    } else {
      [$field, $operator, $value] = $condition;

      if (gettype($value) === "string") {
        $value = "'$value'";
      }

      $where .= "$field $operator $value";
    }

    return $where;
  }

  public static function wheres($conditions): string {
    $wheres = [];

    foreach($conditions as $condition) {
      $wheres[] = static::where($condition, false);
    }

    return " where " . implode(" and ", $wheres);;
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
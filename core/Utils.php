<?php
namespace Core;

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
    } else if (is_bool($value)) {
      $value = $value ? "TRUE" : "FALSE";
    }

    return $toString ? implode(".", array_map(fn($item) => "\"$item\"", explode(".", $field))) . " $operator $value" : [
      "field" => $field,
      "operator" => $operator,
      "value" => $value,
    ];
  }

  public static function selects(array $fields, bool $count = false): string {
    $selects = [];

    if ($count) {
      return "count(*)";
    }

    foreach ($fields as $field) {
      $selects[] = $field !== "*" ? implode(".", array_map(fn($item) => $item === "*" ? $item : "\"$item\"", explode(".", $field))) : $field;
    }

    $res = implode(", ", $selects);

    return $count ? "count($res)" : $res;
  }

  public static function wheres(array $conditions, array $or = [], bool $toString = false): array|string {
    $ors = [...$or];

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

    if (!$toString) {
      return $wheres;
    }

    $string = "";
    $flag = false;

    foreach ($wheres as $index => $where) {
      if ($index === 0 && !$flag && in_array($index, $ors)) {
        $flag = true;
        $string .= "(";
        array_splice($ors, array_search($index, $ors), 1);
      }

      if ($flag && !in_array($index, $ors)) {
        $flag = false;
        $string .= ")";
      }

      if ($index > 0) {
        if (sizeof($ors) > 1 && in_array($index + 1, $ors) && !$flag && in_array($index, $ors)) {
          $flag = true;
          $string .= " AND (";
        } else {
          $string .= in_array($index, $ors) ? " OR " : " AND ";
        }
      }

      if (in_array($index, $ors)) {
        array_splice($ors, array_search($index, $ors), 1);
      }

      $string .= $where;
    }

    if ($flag) {
      $string .= ")";
    }

    return " WHERE $string";
  }

  public static function fields(array $fields, array $appends, ?Collection $collection = null): string {
    $aux = [];

    if ($collection) foreach($fields as $field) {
      $aux[] = "\"$field\"";
    }

    if (!$collection) foreach(array_keys($fields) as $field) {
      if (in_array($field, $appends)) {
        continue;
      }

      $aux[] = "\"$field\"";
    }

    return implode(", ", $aux);
  }

  public static function valueToString(null|bool|string|int $value): string {
    if (is_null($value)) {
      return "NULL";
    } else if (is_string($value)) {
      return "'$value'";
    } else if (is_bool($value)) {
      return $value ? "TRUE" : "FALSE";
    }

    return $value;
  }

  public static function values(array $fields, array $appends, bool $update = false, ?string $identifier = null, ?Collection $collection = null): string {
    $aux = [];

    if ($collection) {
      $aux = $collection->map(function($item) use ($fields) {
        $tmp = [];

        foreach ($fields as $field) {
          $tmp[] = static::valueToString($item[$field] ?? null);
        }

        return "(" . implode(", ", $tmp) . ")";
      });

      return implode(", ", $aux);
    }

    foreach($fields as $field => $value) {
      if (!$update && (is_null($value) || ($field === $identifier && !is_string($field)) || in_array($field, $appends))) {
        continue;
      }

      $value = static::valueToString($value);
      $aux[] = !$update ? $value : "\"$field\" = $value";
    }

    if (!$update) {
      return "(" . implode(", ", $aux) . ")";
    }

    return implode(", ", $aux);
  }

  public static function orders($orders = []) {
    if (sizeof($orders) === 0) {
      return "";
    }

    return " ORDER BY " . implode(", ", array_map(function($order) {
      [$field, $direction] = $order;

      return "\"$field\" $direction";
    }, $orders));
  }

  public static function token(int $size = 32) {
    return bin2hex(random_bytes($size));
  }

  public static function getKey($class, ?string $fk = null): string {
    if (!is_null($fk)) {
      return $fk;
    }

    $classname = explode("\\", $class);
    return strtolower(end($classname)) . "_id";
  }

  public static function getPivot(array $classes): string {
    $tables = array_map(function($class) {
      $classname = explode("\\", $class);

      return strtolower(end($classname));
    }, $classes);

    sort($tables);

    return implode("_", $tables);
  }

  public static function serialize(mixed $serializable): mixed {
    if (is_object($serializable) && method_exists($serializable, "toAssoc")) {
      if ($serializable instanceof Collection) {
        foreach ($serializable as &$item) {
          $item = static::serialize($item);
        }
      }

      return static::serialize($serializable->toAssoc());
    }

    if (is_array($serializable)) foreach($serializable as &$item) {
      $item = static::serialize($item);
    }

    return $serializable;
  }

  public static function flatten(array $arr): array {
    $res = [];

    foreach ($arr as $item) {
      $res = array_merge($res, $item);
    }

    return $res;
  }

  public static function print(...$printables) {
    foreach ($printables as $printable) {
      if (is_object($printable) && method_exists($printable, "toJson")) {
        print($printable->toJson());
      } else {
        print(json_encode($printable, JSON_PRETTY_PRINT));
      }
  
      print("\n");
    }
  }

  public static function dump(...$printables) {
    var_dump(...$printables);
  }

  public static function dd(...$printables) {
    http_response_code(500);
    static::dump(...$printables);
    exit();
  }
}
<?php
include_once("utils/Connection.php");
include_once("Collection.php");

class Model extends ArrayObject {
  protected $table;
  protected $identifier;
  protected $fillable = [];
  protected $hidden = [];
  protected $appends = [];

  public function __construct(array $fields = [], $ignoreFillable = false) {
    $this->fill($fields, $ignoreFillable);
  }

  public function __set($name, $val) {
    $this[$name] = $val;
  }

  public function __get($name) {
    if (isset($this[$name])) {
      return $this[$name];
    }

    if (in_array($name, $this->appends) && $function = $this->attribute($name)) {
      return $this->{$function}();
    }
  }

  public static function find($id) {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";
    $identifier = $instance->identifier ?? "id";
    
    try {
      $conn = (new Connection())->getConnection();
      $sql = "SELECT * FROM $table where $identifier = $id";
      $query = $conn->query($sql);

      return $instance->fill($query->fetch(PDO::FETCH_ASSOC), true);
    } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
    } finally {
      $conn = null;
    }
  }

  public static function get(...$fields) {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";

    if (sizeof($fields) === 1 && gettype($fields[0]) === "array") {
      $fields = implode(", ", $fields[0]);
    } else if(sizeof($fields) >= 1) {
      $fields = implode(", ", $fields);
    } else {
      $fields = "*";
    }

    try {
      $conn = (new Connection())->getConnection();
      $sql = "SELECT $fields FROM $table";
      $query = $conn->query($sql);
      $arr = [];

      while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr[] = new static($row, true);
      }

      return new Collection($arr);
    } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
    } finally {
      $conn = null;
    }
  }

  private function attribute($name) {
    if (method_exists($this, $name)) {
      return $name;
    }

    $function = "get" . str_replace("_", "", $name) . "attribute";

    if (method_exists($this, $function)) {
      return $function;
    }

    return false;
  }

  public function fill($fields = [], $ignoreFillable = false) {
    foreach($fields as $field => $value) {
      if ($ignoreFillable || in_array($field, $this->fillable)) {
        $this->{$field} = $value;
      }
    }

    foreach($this->appends as $append) {
      if ($name = $this->attribute($append)) {
        $this->{$append} = $this->{$name}();
      }
    }

    return $this;
  }

  public function arraylize() {
    $arr = [];

    foreach($this as $field => $value) {
      if (!in_array($field, $this->hidden)) {
        $arr[$field] = $value;
      }
    }

    return $arr;
  }

  public function __toString() {
    return json_encode($this, JSON_PRETTY_PRINT);
  }
}
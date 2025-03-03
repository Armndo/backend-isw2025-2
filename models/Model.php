<?php
include_once("utils/Connection.php");
include_once("utils/Utils.php");
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

    return $instance->fill(Utils::runQuery("*", $table, "$identifier = $id")[0], true);
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

    return new Collection(
      array_map(function ($fields) {
        return new static($fields, true);
      }, Utils::runQuery($fields, $table, ""))
    );
  }

  public static function search(...$conditions) {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";
    $where = "";

    if (in_array(sizeof($conditions), [2, 3]) && array_reduce($conditions, function ($a, $b) { return $a && gettype($b) !== "array"; }, true)) {
      $where = Utils::where($conditions);
    } else if(sizeof($conditions) === 1) {
      $where = Utils::wheres($conditions[0]);
    }

    return new Collection(
      array_map(function ($fields) {
        return new static($fields, true);
      }, Utils::runQuery("*", $table, $where))
    );
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
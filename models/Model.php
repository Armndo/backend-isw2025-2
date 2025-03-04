<?php
include_once("utils/Connection.php");
include_once("utils/Utils.php");
include_once("Collection.php");
include_once("Query.php");

class Model extends ArrayObject {
  protected $table;
  protected $identifier;
  protected $fillable = [];
  protected $hidden = [];
  protected $appends = [];

  public function __construct(array | null $fields = [], $ignoreFillable = false) {
    if ($fields === null) {
      return ;
    }

    $this->fill($fields, $ignoreFillable);
  }

  public function __set($name, $val) {
    $this[$name] = $val;
  }

  public function __get($name) {
    if (isset($this[$name])) {
      return $this[$name];
    }

    if (in_array($name, $this->appends) && $function = $this->getFunction($name)) {
      return $this->{$function}();
    }
  }

  public static function where(...$conditions) {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";
    $identifier = $instance->identifier ?? "id";

    return new Query(static::class, $table, $identifier)->where(...$conditions);
  }

  public static function find($id) {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";
    $identifier = $instance->identifier ?? "id";

    return new Query(static::class, $table, $identifier)->find($id);
  }

  public static function get() {
    $instance = new static();
    $table = $instance->table ?? strtolower($instance::class) . "s";
    $identifier = $instance->identifier ?? "id";

    return new Query(static::class, $table, $identifier)->get();
  }

  private function getFunction($name) {
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
      if ($name = $this->getFunction($append)) {
        $this->{$append} = $this->{$name}();
      }
    }

    return $this;
  }

  public function toAssoc() {
    $arr = [];

    foreach($this as $field => $value) {
      if (!in_array($field, $this->hidden)) {
        $arr[$field] = $value;
      }
    }

    return $arr;
  }

  public function toJson() {
    return json_encode($this->toAssoc(), JSON_PRETTY_PRINT);
  }

  public function __toString() {
    return json_encode($this, JSON_PRETTY_PRINT);
  }
}
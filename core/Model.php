<?php
namespace Core;

use ArrayObject;

class Model extends ArrayObject {
  use Queryable;
  use Storable;
  use Relatable;

  protected $table;
  protected $identifier;
  protected $lastIdentifier = null;
  protected $fillable = [];
  protected $hidden = [];
  protected $appends = [];
  protected $stored = false;

  public function __construct(?array $fields = [], bool $ignoreFillable = false, bool $stored = false) {
    if ($fields === null) {
      return ;
    }

    if (!isset($this->table)) {
      $class = explode("\\", $this::class);
      $this->table = strtolower(end($class)) . "s";
    }

    if (!isset($this->identifier)) {
      $this->identifier = "id";
    }

    $this->fill($fields, $ignoreFillable);
    $this->stored = $stored;

    if ($stored) {
      $this->lastIdentifier = $this->{$this->identifier};
    }
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

  public function getIdentifier() {
    return $this->identifier;
  }

  public function getLastIdentifier() {
    return $this->lastIdentifier;
  }

  public function getTable() {
    return $this->table;
  }

  public function setTable(string $table) {
    $this->table = $table;
  }

  public function getFillable(): array {
    return $this->fillable;
  }

  public function setFillable(array $fillable) {
    $this->fillable = $fillable;
  }

  public function getFields(): array {
    $fields = [];

    foreach($this as $field => $value) {
      if (in_array($field, $this->appends)) {
        continue;
      }

      $fields[$field] = $value;
    }

    return $fields;
  }

  public function getAppends() {
    return $this->appends;
  }

  public function getStored(): bool {
    return $this->stored;
  }

  private function getFunction(string $name): bool|string {
    if (method_exists($this, $name)) {
      return $name;
    }

    $function = "get" . str_replace("_", "", $name) . "attribute";

    if (method_exists($this, $function)) {
      return $function;
    }

    return false;
  }

  public function fill($fields = [], bool $ignoreFillable = false, bool $stored = false): static {
    if (!$this->stored) {
      $this->stored = $stored;
    }

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

    if ($stored) {
      $this->lastIdentifier = $this->{$this->identifier};
    }

    return $this;
  }

  public function appends(string|array $appendables): static {
    if (is_string($appendables)) {
      $appendables = [$appendables];
    }

    foreach ($appendables as $appendable) {
      if (!isset($this->$appendable) && method_exists(static::class, $this->getFunction($appendable))) {
        $this->{$appendable} = $this->{$this->getFunction($appendable)}();
      }
    }

    return $this;
  }

  public function toAssoc(bool $ignore = false): array {
    $arr = [];

    foreach($this as $field => $value) {
      if ($ignore || !in_array($field, $this->hidden)) {
        $arr[$field] = $value;
      }
    }

    return $arr;
  }

  public function toJson(bool $ignore = false): string {
    return json_encode($this->toAssoc($ignore), JSON_PRETTY_PRINT);
  }
}
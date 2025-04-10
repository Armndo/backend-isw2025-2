<?php
namespace Core;

trait Relatable {
  public function has($class, $many = false, $fk = null) {
    $fk = $this->getKey($this::class, $fk);
    $pk = $this->identifier;

    $query = $class::where($fk, $this->$pk);

    return !$many ? $query->first() : $query->get();
  }

  public function belongs($class, $many = false, $pivot = null, $fk = null, $pk = null) {
    $fk = $this->getKey($class, $fk);

    if (!$many) {
      return $class::find($this->$fk);
    }

    if ($pivot === null) {
      $pivot = $this->getPivot([$class, $this::class]);
    }
    
    $pk = $this->getKey($this::class, $pk);
    $instance = new $class;
    $identifier = $this->identifier;

    return $instance
    ->select($instance->table . ".*")
    ->join($pivot, "$pivot.$fk", "$instance->table." . $instance->getIdentifier())
    ->join($this->table, "$this->table.$this->identifier", "$pivot.$pk")
    ->where("$pivot.$pk", $this->$identifier);
  }

  private function getKey($class, $fk) {
    if (!is_null($fk)) {
      return $fk;
    }

    $classname = explode("\\", $class);
    return strtolower(end($classname)) . "_id";
  }

  private function getPivot(array $classes) {
    $tables = array_map(function($class) {
      $classname = explode("\\", $class);

      return strtolower(end($classname));
    }, $classes);

    sort($tables);

    return implode("_", $tables);
  }
}
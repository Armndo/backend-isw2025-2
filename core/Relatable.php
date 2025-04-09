<?php
namespace Core;

trait Relatable {
  public function has($class, $many = false, $fk = null) {
    $fk = $this->getFk($this::class, $fk);
    $pk = $this->identifier;

    $query = $class::where($fk, $this->$pk);

    return !$many ? $query->first() : $query->get();
  }

  public function belongs($class, $many = false, $fk = null) {
    $fk = $this->getFk($class, $fk);

    if (!$many) {
      return $class::find($this->$fk);
    }

    // return $class::where()
  }

  private function getFk($class, $fk) {
    if (!is_null($fk)) {
      return $fk;
    }

    $classname = explode("\\", $class);
    return strtolower(end($classname)) . "_id";
  }
}
<?php
namespace Core;

trait Relatable {
  public function has(string $class, bool $many = false, ?string $fk = null): null|Model|Collection {
    $fk = Utils::getKey($this::class, $fk);
    $pk = $this->identifier;

    $query = $class::where($fk, $this->$pk);

    return !$many ? $query->first() : $query->get();
  }

  public function belongs(string $class, bool $many = false, ?string $pivot = null, ?string $fk = null, ?string $pk = null, bool $asQuery = false, ?string $orderBy = null, ?string $direction = "ASC"): null|Query|Model|Collection {
    $fk = Utils::getKey($class, $fk);

    if (!$many) {
      return $this->$fk ? $class::find($this->$fk) : null;
    }

    if ($pivot === null) {
      $pivot = Utils::getPivot([$class, $this::class]);
    }

    $pk = Utils::getKey($this::class, $pk);
    $instance = new $class;
    $identifier = $this->identifier;

    $query = $instance
    ->select($instance->table . ".*")
    ->join($pivot, "$pivot.$fk", "$instance->table." . $instance->getIdentifier())
    ->join($this->table, "$this->table.$this->identifier", "$pivot.$pk")
    ->where("$pivot.$pk", $this->$identifier);

    return $asQuery ? $query : $query
    ->orderBy($orderBy ?? $instance->getIdentifier(), $direction)
    ->get()
    ->unique();
  }
}
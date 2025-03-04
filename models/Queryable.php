<?php
include_once("Query.php");

trait Queryable {
  public static function where(...$conditions) {
    $instance = new static();

    return (new Query(static::class, $instance->table, $instance->identifier))->where(...$conditions);
  }

  public static function whereRaw(string $raw) {
    $instance = new static();

    return (new Query(static::class, $instance->table, $instance->identifier))->whereRaw($raw);
  }
  
  public static function orderBy($field, $direction = "ASC") {
    $instance = new static();

    return (new Query(static::class, $instance->table, $instance->identifier))->orderBy($field, $direction);
  }

  public static function find($id) {
    $instance = new static();

    return (new Query(static::class, $instance->table, $instance->identifier))->find($id);
  }

  public static function get() {
    $instance = new static();

    return (new Query(static::class, $instance->table, $instance->identifier))->get();
  }
}
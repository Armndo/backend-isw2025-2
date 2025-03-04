<?php
trait Queryable {
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
}
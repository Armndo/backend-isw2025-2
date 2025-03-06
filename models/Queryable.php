<?php
namespace Models;

trait Queryable {
  public static function where(...$conditions) {
    return (new Query(new static))->where(...$conditions);
  }

  public static function whereRaw(string $raw) {
    return (new Query(new static))->whereRaw($raw);
  }
  
  public static function orderBy($field, $direction = "ASC") {
    return (new Query(new static))->orderBy($field, $direction);
  }

  public static function find($id) {
    return (new Query(new static))->find($id);
  }

  public static function get() {
    return (new Query(new static))->get();
  }
}
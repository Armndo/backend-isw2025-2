<?php
namespace Core;

trait Queryable {
  public static function select(...$selects): Query {
    return (new Query(new static))->select(...$selects);
  }

  public static function where(...$conditions): Query {
    return (new Query(new static))->where(...$conditions);
  }

  public static function join(string $table, string $first, string $second): Query {
    return (new Query(new static))->join($table, $first, $second);
  }

  public static function whereRaw(string $raw): Query {
    return (new Query(new static))->whereRaw($raw);
  }
  
  public static function orderBy($field, $direction = "ASC"): Query {
    return (new Query(new static))->orderBy($field, $direction);
  }

  public static function find(string | int $id): static | null {
    return (new Query(new static))->find($id);
  }

  public static function first(): static {
    return (new Query(new static))->first();
  }

  public static function get(): Collection {
    return (new Query(new static))->get();
  }
}
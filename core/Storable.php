<?php
namespace Core;

trait Storable {
  public static function create(array $items): null|static|Collection {
    return (new Query(new static))->create($items);
  }

  public function save(): static {
    return (new Query($this))->save();
  }
}
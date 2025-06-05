<?php
namespace Core;

trait Storable {
  public static function create(array $items, ?Model $model = null): null|static|Collection {
    return (new Query($model ?? new static))->create($items, !!$model);
  }

  public function save(): static {
    return (new Query($this))->save();
  }
}
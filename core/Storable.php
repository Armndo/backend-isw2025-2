<?php
namespace Core;

trait Storable {
  public static function create(array $items, ?Model $model = null): null|static|Collection {
    return (new Query($model ?? new static))->create($items, !!$model);
  }

  public function save(): static {
    return (new Query($this))->save();
  }

  public static function update(array $fields): Collection {
    return (new Query(new static))->update($fields);
  }

  public function delete(array $wheres = [], array $orWheres = []): bool {
    return (new Query($this))->delete($wheres, $orWheres);
  }

  public function attach(string $class, int|string|array $ids, ?string $table = null) {
    (new Query($this))->attach($class, $ids, $table);
  }

  public function detach(string $class, int|string|array $ids = [], ?string $table = null) {
    (new Query($this))->detach($class, $ids, $table);
  }

  public function sync(string $class, array $ids = [], ?string $table = null) {
    (new Query($this))->sync($class, $ids, $table);
  }
}
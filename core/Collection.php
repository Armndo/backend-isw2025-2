<?php
namespace Core;

use ArrayAccess;
use IteratorAggregate;
use Traversable;
use ArrayIterator;
use Countable;

class Collection implements ArrayAccess, IteratorAggregate, Countable {
  private $items = [];

  public function __construct(array $items = []) {
    $this->items = $items;
  }

  public function offsetSet($key, $value): void {
    if (is_null($key)) {
      $this->items[] = $value;
    } else {
      $this->items[$key] = $value;
    }
  }

  public function offsetExists($key): bool {
    return isset($this->items[$key]);
  }

  public function offsetUnset($key): void {
    unset($this->items[$key]);
  }

  public function offsetGet($key): mixed {
    return $this->items[$key];
  }

  public function getIterator(): Traversable {
    return new ArrayIterator($this->items);
  }

  public function count(): int {
    return sizeof($this->items);
  }

  public function map(?callable $callback = null, bool $asArray = true ): array|Collection {
    if ($callback && !$asArray) {
      return new Collection(
        array_map($callback, $this->items)
      );
    }

    $res = [];

    foreach ($this->items as $item) {
      $res[] = $callback ? $callback($item) : $item;
    }

    return $res;
  }

  public function each(callable $callback) {
    foreach ($this->items as $item) {
      $callback($item);
    }
  }

  public function push(mixed $item): self {
    $this->items[] = $item;

    return $this;
  }

  public function concat(Collection $collection): self {
    foreach ($collection as $item) {
      $this->items[] = $item;
    }

    return $this;
  }

  public function toJson(): string {
    return json_encode($this->toAssoc(), JSON_PRETTY_PRINT);
  }

  public function toAssoc(): array {
    $arr = [];

    foreach($this->items as $item) {
      $arr[] = $item->toAssoc();
    }

    return $arr;
  }

  public function toArray(): array {
    $arr = [];

    foreach($this->items as $item) {
      $arr[] = array_values((array) $item);
    }

    return $arr;
  }

  public function __toString() {
    return json_encode($this->items, JSON_PRETTY_PRINT);
  }
}
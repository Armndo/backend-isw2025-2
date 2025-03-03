<?php
class Collection implements ArrayAccess, IteratorAggregate {
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

  public function toJson() {
    return json_encode($this->toAssoc(), JSON_PRETTY_PRINT);
  }

  public function toAssoc() {
    $arr = [];

    foreach($this->items as $item) {
      $arr[] = $item->arraylize();
    }

    return $arr;
  }

  public function toArray() {
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
<?php
namespace Core;

trait Storable {
  public function save(): static {
    return (new Query($this))->save();
  }
}
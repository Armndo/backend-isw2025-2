<?php
namespace Core;

trait Storable {
  public function save() {
    return (new Query($this))->save();
  }
}
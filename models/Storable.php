<?php
namespace Models;

trait Storable {
  public function save() {
    return (new Query($this))->save();
  }
}
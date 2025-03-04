<?php
include_once("Query.php");

trait Storable {
  public function save() {
    return (new Query($this))->save();
  }
}
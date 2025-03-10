<?php
namespace Models;

use Core\Model;

class User extends Model {
  protected $fillable = [
    "name",
    "lastname",
    "age",
  ];

  protected $hidden = [
    "name",
    "lastname",
  ];

  protected $appends = [
    "full_name"
  ];

  public function getFullnameAttribute() {
    return $this->name . ($this->lastname ? " $this->lastname" : "");
  }
}
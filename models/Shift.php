<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Shift extends Model {
  protected $fillable = [
    "name",
  ];

  protected $hidden = [
    "id"
  ];

  // public function users(): Collection {
  //   return $this->belongs(User::class, true);
  // }
}
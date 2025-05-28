<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Group extends Model {
  protected $fillable = [
    "name",
    "shift_id",
    "major_id",
  ];

  protected $hidden = [
    "id",
    "shift_id",
    "major_id",
  ];

  // public function users(): Collection {
  //   return $this->belongs(User::class, true);
  // }
}
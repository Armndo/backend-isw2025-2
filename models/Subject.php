<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Subject extends Model {
  protected $fillable = [
    "name",
    "major_id",
  ];

  protected $hidden = [
    "id",
    "major_id",
  ];

  // public function users(): Collection {
  //   return $this->belongs(User::class, true);
  // }
}
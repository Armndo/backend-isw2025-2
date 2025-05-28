<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Major extends Model {
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
<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Visitor extends Model {
  protected $fillable = [
    "id",
    "name",
    "age",
    "sex",
  ];

  public function visits(): Collection {
    return $this->has(Visit::class, true);
  }
}
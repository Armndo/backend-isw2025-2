<?php
namespace Models;

use Core\Collection;
use Core\Model;
use Core\Query;

class Group extends Model {
  protected $fillable = [
    "name",
    "shift_id",
    "major_id",
  ];

  protected $hidden = [
    "shift_id",
    "major_id",
  ];

  public function students(bool $asQuery = false): Query|Collection {
    return $this->belongs(Student::class, true, "enrolled", asQuery: $asQuery);
  }
}
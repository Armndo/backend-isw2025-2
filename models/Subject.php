<?php
namespace Models;

use Core\Collection;
use Core\Model;
use Core\Query;

class Subject extends Model {
  protected $fillable = [
    "name",
    "semester",
    "major_id",
  ];

  protected $hidden = [
    "major_id",
  ];

  public function students(bool $asQuery = false): Query|Collection {
    return $this->belongs(Student::class, true, "enrolled", asQuery: $asQuery);
  }

  public function groups(bool $asQuery = false): Query|Collection {
    return $this->belongs(Group::class, true, "enrolled", asQuery: $asQuery);
  }
}
<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Project extends Model {
  protected $fillable = [
    "name",
    "description",
    "group_id",
    "subject_id",
  ];

  protected $hidden = [
    "id",
    "group_id",
    "subject_id",
  ];

  public function students(): Collection {
    return $this->belongs(Student::class, true);
  }
}
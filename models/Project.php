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
    "group_id",
    "subject_id",
  ];

  public function getOwnedAttribute() {
    return true;
  }

  public function group(): Group {
    return $this->belongs(Group::class);
  }

  public function subject(): Subject {
    return $this->belongs(Subject::class);
  }

  public function students(): Collection {
    return $this->belongs(Student::class, true);
  }
}
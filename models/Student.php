<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Student extends Model {
  protected $fillable = [
    "id",
    "user_id",
    "major_id",
  ];

  protected $hidden = [
    "user_id",
    "major_id",
  ];

  public function user(): User {
    return $this->belongs(User::class);
  }

  public function projects(): Collection {
    return $this->belongs(Project::class, true);
  }
}
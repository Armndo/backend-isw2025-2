<?php
namespace Models;

use Core\Collection;
use Core\Model;
use Core\Query;

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

  public function getNameAttribute() {
    $user = $this->user();

    return trim("$user?->name $user?->paternal_lastname $user->maternal_lastname");
  }

  public function user(): User {
    return $this->belongs(User::class);
  }

  public function projects(): Collection {
    return $this->belongs(Project::class, true);
  }

  public function groups(): Collection {
    return $this->belongs(Group::class, true, "enrolled");
  }

  public function subjects(bool $asQuery = false): Query|Collection {
    return $this->belongs(Subject::class, true, "enrolled", asQuery: $asQuery);
  }
}
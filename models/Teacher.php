<?php
namespace Models;

use Core\Collection;
use Core\Model;

class Teacher extends Model {
  protected $fillable = [
    "id",
    "user_id",
  ];

  protected $hidden = [
    "user_id",
  ];

  public function user(): User {
    return $this->belongs(User::class);
  }

  public function subjects(): Collection {
    return $this->belongs(Subject::class, true, "taught");
  }

  public function groups(): Collection {
    return $this->belongs(Group::class, true, "taught");
  }
}
<?php
namespace Models;

use Core\Collection;
use Core\Model;
use Core\Session;

class User extends Model {
  protected $fillable = [
    "name",
    "paternal_lastname",
    "maternal_lastname",
    "type",
    "email",
    "password",
  ];

  protected $hidden = [
    "password",
  ];

  public static function check($credentials): ?Session {
    return static::where("email", $credentials["email"])->where("password", $credentials["password"])->first();
  }

  public function isAdmin(): bool {
    return $this->type === "admin";
  }

  public function isStudent(): bool {
    return $this->type === "student";
  }

  public function isTeacher(): bool {
    return $this->type === "teacher";
  }

  public function sessions(): Collection {
    return $this->has(Session::class, true);
  }

  public function student(): Student {
    return $this->has(Student::class);
  }

  public function teacher(): Teacher {
    return $this->has(Teacher::class);
  }
}
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
    "email",
    "password",
  ];

  public static function check($credentials) {
    return static::where("email", $credentials["email"])->where("password", $credentials["password"])->first();
  }

  public function sessions() {
    return $this->has(Session::class, true);
  }
}
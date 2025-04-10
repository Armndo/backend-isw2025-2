<?php
namespace Models;

use Core\Model;

class Student extends Model {
  protected $fillable = [
    "id",
    "name",
    "paternal_lastname",
    "maternal_lastname",
    "email",
    //"project",
  ];

  protected $hidden = [
    "id",
    "name",
    "paternal_lastname",
    "maternal_lastname",
  ];

  protected $appends = [
    "full_name",
  ];


  public function getFullnameAttribute() {
    return $this->name . " $this->paternal_lastname" .
    ($this->maternal_lastname ? " $this->maternal_lastname" : "");
  }

  public function projects() {
    return $this->belongs(Project::class, true);
    return Project::where("user_id", $this->id)->get();
  }

}
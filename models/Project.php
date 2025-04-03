<?php
namespace Models;

use Core\Model;

class Project extends Model {
  protected $fillable = [
    "name",
    "user_id",
  ];

  protected $hidden = [
    "user_id",
  ];
}
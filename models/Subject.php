<?php
namespace Models;

use Core\Model;

class Subject extends Model {
  protected $fillable = [
    "name",
    "major_id",
  ];

  protected $hidden = [
    "major_id",
  ];
}
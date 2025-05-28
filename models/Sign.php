<?php

namespace Models;

use Core\Model;

class Sign extends Model
{
  protected $fillable = [
    "sign",
    "printed",
    "project_id",
  ];

  public function project() {
    return $this->belongs(Project::class);
  }
}

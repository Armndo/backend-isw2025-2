<?php
namespace Models;

use Core\Model;

class Visit extends Model {
  protected $fillable = [
    "comment",
    "rating",
    "date",
    "counter",
    "visitor_id",
    "project_id",
  ];

  protected $hidden = [
    "id",
    "counter",
    "visitor_id",
    "project_id",
  ];

  public function visitor(): Visitor {
    return $this->belongs(Visitor::class);
  }

  public function project(): Project {
    return $this->belongs(Project::class);
  }
}
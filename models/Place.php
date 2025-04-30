<?php

namespace Models;
use Core\Model;

class Place extends Model {
  protected $fillable = [
    "name",
    "description",
    "latitude",
    "longitude",
  ];
}
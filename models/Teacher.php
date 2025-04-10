<?php
namespace Models;

use Core\Model;

class Teacher extends Model
{
    protected $fillable=[
        "id",
        "name",
        "paternal_lastname",//Apellido paterno
        "maternal_lastname",//Apellido Materno
        "email",
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
}
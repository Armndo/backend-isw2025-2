<?php

namespace Models;
use Core\Model;

class Jury extends Model
{
    protected $fillable = [
        "id",
        // "teacher_id",
    ];

    // Relación con Teacher (un jurado pertenece a un profesor)
    // public function teacher()
    // {
    //     return $this->belongsTo(Teacher::class, 'teacher_id');
    // }

    // // Relación con Project (un jurado puede estar asociado a muchos proyectos)
    // public function projects()
    // {
    //     return $this->hasMany(Project::class, 'jury_id');
    // }
}
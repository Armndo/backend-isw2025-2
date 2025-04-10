<?php
namespace Controllers;

use Core\Controller;
use Models\Teacher;

class TeacherController extends Controller {
  public function index() {
    return Teacher::get()->toJson();
  }

  public function view($id) {
    return Teacher::find($id)->toJson();
  }

  public function store() {
    $teacher = new Teacher($this->request->only([
      "id",
      "name",
      "paternal_lastname",//Apellido paterno
      "maternal_lastname",//Apellido Materno
      "email",
    ]));
  }
}
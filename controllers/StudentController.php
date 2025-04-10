<?php
namespace Controllers;

use Core\Controller;
use Models\Student;

class StudentController extends Controller{
  public function index() {
    return Student::get()->toJson();
  }

  public function view($id) {
    return Student::find($id)->toJson();
  }

  public function store()
  {
    $student = new Student($this->request->only([
      "id",
      "name",
      "paternal_lastname",
      "maternal_lastname",
      "email",
    ]));
  }
}
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

  public function store() {
    $student = new Student($this->request->only([
      "id",
      "name",
      "paternal_lastname",
      "maternal_lastname",
      "email",
      "major_id"
    ]));

    return $student->save();
  }

  public function update($id) {
    $student = Student::find($id);

    if (!$student) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $student->fill($this->request->only([
      "name",
      "paternal_lastname",
      "maternal_lastname",
      "email",
      "major_id"
    ]));

    return $student;
  }
}
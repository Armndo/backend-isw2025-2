<?php
namespace Controllers;

use Core\Controller;
use Models\Student;
use Models\User;

class StudentController extends Controller{
  public function index() {
    return Student::get()->toJson();
  }

  public function view($id) {
    if (!$this->user?->isAdmin() && $this->user?->student()?->id !== $id) {
      http_response_code(401);
      return json_encode(["error" => true, "message" => "Unauthorized."]);
    }

    return Student::find($id)->toJson();
  }

  public function store() {
    if (User::where("email", $this->request->email)->first()) {
      http_response_code(500);
      return json_encode(["error" => true, "message" => "Email already registered."]);
    }

    if (Student::find($this->request->id)) {
      http_response_code(500);
      return json_encode(["error" => true, "message" => "Student already registered."]);
    }

    $user = new User([
      ...$this->request->only([
        "name",
        "paternal_lastname",
        "maternal_lastname",
        "email",
        "password",
      ]),
      "type" => "student",
    ])->save();

    return new Student([
      ...$this->request->only([
        "id",
        "major_id",
      ]),
      "user_id" => $user->id,
    ])->save();
  }

  public function update($id) {
    if (!$this->user?->isAdmin() && $this->user?->student()?->id !== $id) {
      http_response_code(401);
      return json_encode(["error" => true, "message" => "Unauthorized."]);
    }

    $student = Student::find($id);

    if (!$student) {
      http_response_code(400);
      return json_encode(["error" => true, "message" => "Student doesn't exist."]);
    }

    // $student->fill($this->request->only([
    //   "name",
    //   "paternal_lastname",
    //   "maternal_lastname",
    //   "email",
    //   "major_id"
    // ]));

    // return $student;

    return 1;
  }
}
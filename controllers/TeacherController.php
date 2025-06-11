<?php
namespace Controllers;

use Core\Controller;
use Models\Group;
use Models\Subject;
use Models\Teacher;
use Models\User;

class TeacherController extends Controller {
  public function index() {
    if ($this->user?->isTeacher()) {
      return Teacher::where("id", "!=", $this->user?->teacher()?->id)->get();
    }

    if ($this->user?->isAdmin()) {
      return Teacher::get();
    }

    http_response_code(401);
    return ["error" => true, "message" => "Unauthorized."];
  }

  public function view($id) {
    if (!$this->user?->isAdmin() && $this->user?->teacher()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Teacher::find($id);
  }

  public function store() {
    if (User::where("email", $this->request->email)->first()) {
      http_response_code(500);
      return ["error" => true, "message" => "Email already registered."];
    }

    if (Teacher::find($this->request->id)) {
      http_response_code(500);
      return ["error" => true, "message" => "Teacher already registered."];
    }

    $user = new User([
      ...$this->request->only([
        "name",
        "paternal_lastname",
        "maternal_lastname",
        "email",
        "password",
      ]),
      "type" => "teacher",
    ])->save();

    new Teacher([
      ...$this->request->only([
        "id",
      ]),
      "user_id" => $user->id,
    ])->save();

    return "Ok";
  }

  public function update($id) {
    if (!$this->user?->isAdmin() && $this->user?->teacher()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $teacher = Teacher::find($id);

    if (!$teacher) {
      http_response_code(400);
      return ["error" => true, "message" => "Teacher doesn't exist."];
    }

    $teacher->fill($this->request->only([
      "id",
    ]))->save();

    $teacher->user()->fill($this->request->only([
      "name",
      "paternal_lastname",
      "maternal_lastname",
      "email",
      "password"
    ]))->save();

    return "Ok";
  }

  public function teach($id) {
    if (!$this->user?->isAdmin() && $this->user?->teacher()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $teacher = Teacher::find($id);

    if (!$teacher) {
      http_response_code(400);
      return ["error" => true, "message" => "Teacher doesn't exist."];
    }

    $group = Group::find($this->request->group_id);

    if (!$group) {
      http_response_code(400);
      return ["error" => true, "message" => "Group doesn't exist."];
    }

    $subject = Subject::find($this->request->subject_id);

    if (!$subject) {
      http_response_code(400);
      return ["error" => true, "message" => "Subject doesn't exist."];
    }

    $teacher->attach(Subject::class, [$subject->id => ["group_id" => $group->id]], "taught", ["group_id"]);

    return "Ok";
  }

  public function subjects($id) {
    if (!$this->user?->isAdmin() && $this->user?->teacher()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $teacher = Teacher::find($id);

    if (!$teacher) {
      http_response_code(400);
      return ["error" => true, "message" => "Teacher doesn't exist."];
    }

    return $teacher->subjects();
  }

  public function groups($id) {
    if (!$this->user?->isAdmin() && $this->user?->teacher()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $teacher = Teacher::find($id);

    if (!$teacher) {
      http_response_code(400);
      return ["error" => true, "message" => "Teacher doesn't exist."];
    }

    return $teacher->groups();
  }
}
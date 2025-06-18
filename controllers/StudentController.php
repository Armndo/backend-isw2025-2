<?php
namespace Controllers;

use Core\Controller;
use Exception;
use Models\Group;
use Models\Project;
use Models\Student;
use Models\Subject;
use Models\User;

class StudentController extends Controller {
  public function index() {
    if ($this->user?->isStudent()) {
      return Student::where("id", "!=", $this->user?->student()?->id)->get();
    }

    if ($this->user?->isAdmin()) {
      return Student::get();
    }

    http_response_code(401);
    return ["error" => true, "message" => "Unauthorized."];
  }

  public function view($id) {
    if (!$this->user?->isAdmin() && $this->user?->student()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Student::find($id);
  }

  public function search(bool $inProject = true) {
    $isStudent = $this->user?->isStudent();

    if (!$this->user?->isAdmin() && !$isStudent) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $query = $this->request->query ?? "";
    
    if (!$inProject) {
      return Student::whereRaw("id ILIKE '%$query%'")->get();
    }

    $ignore = $this->request->ignore ?? [];
    $project = Project::find(+$this->request->project_id);

    if (!$project) {
      http_response_code(404);
      return ["error" => true, "message" => "Project doesn't exist."];
    }

    $student = $this->user?->student();

    if ($isStudent && !$project->students()->has($student)) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $group = $project->group();
    
    if ($isStudent && !in_array($student?->id, $ignore)) {
      $ignore[] = $student->id;
    }

    $inIgnore = implode(",", array_map(fn($item) => "'$item'", $ignore));

    return $query !== "" && !empty($ignore) ? $group->students(true)->whereRaw("students.id ILIKE '%$query%' AND students.id NOT IN ($inIgnore) AND subject_id = $project->subject_id")->get()->unique()->appends("name") : [];
  }

  public function search2() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return $this->search(false);
  }

  public function store() {
    if (User::where("email", $this->request->email)->first()) {
      http_response_code(400);
      return ["error" => true, "message" => "Email already registered."];
    }

    if (Student::find($this->request->id)) {
      http_response_code(400);
      return ["error" => true, "message" => "Student already registered."];
    }

    try {
      $user = (new User([
        ...$this->request->only([
          "name",
          "paternal_lastname",
          "maternal_lastname",
          "email",
          "password",
        ]),
        "type" => "student",
      ]))->save();

      (new Student([
        ...$this->request->only([
          "id",
          "major_id",
        ]),
        "user_id" => $user->id,
      ]))->save();
    } catch (Exception) {
      $user?->delete() ?? null;
      http_response_code(400);
      return ["error" => true, "message" => "Couldn't store student."];
    }

    return "Ok";
  }

  public function update($id) {
    if (!$this->user?->isAdmin() && $this->user?->student()?->id !== $id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $student = Student::find($id);

    if (!$student) {
      http_response_code(404);
      return ["error" => true, "message" => "Student doesn't exist."];
    }

    try {
      $student->fill($this->request->only([
        "id",
      ]))->save();

      $student->user()->fill($this->request->only([
        "name",
        "paternal_lastname",
        "maternal_lastname",
        "email",
        "password"
      ]))->save();
    } catch (Exception) {
      http_response_code(400);
      return ["error" => true, "message" => "Couldn't update student."];
    }

    return "Ok";
  }

  public function preenroll(bool $admin = false) {
    if (!$admin && $this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    if (!$this->user?->isAdmin() && !$this->user?->isStudent()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $student = $this->user?->isStudent() ? $this->user?->student() : Student::find($this->request->id);
    $group = Group::find(+($this->request->group_id ?? 0));

    return $student ? [
      "groups" => Group::get(),
      "subjects" => $group ? Subject::whereRaw("id NOT IN (SELECT subject_id FROM enrolled WHERE student_id = '$student->id') AND semester = $group->semester")->get() : [],
    ] : [
      "groups" => [],
      "subjects" => [],
    ];
  }

  public function enroll() {
    if (!$this->user?->isAdmin() && !$this->user?->isStudent()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $student = $this->user?->isStudent() ? $this->user?->student() : Student::find($this->request->id);

    if (!$student) {
      http_response_code(400);
      return ["error" => true, "message" => "Student doesn't exist."];
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

    try {
      if (!$student->attach(Subject::class, [$subject->id => ["group_id" => $group->id]], "enrolled", ["group_id"])) {
        http_response_code(400);
        return ["error" => true, "message" => "Couldn't enroll."];
      }
    } catch (Exception) {
      http_response_code(400);
      return ["error" => true, "message" => "Couldn't enroll."];
    }

    return "Ok";
  }
}
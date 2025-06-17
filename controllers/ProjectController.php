<?php
namespace Controllers;

use Core\Controller;
use Models\Group;
use Models\Project;
use Models\Student;
use Models\Subject;

class ProjectController extends Controller {
  public function index() {
    return Project::get();
  }

  public function view($id) {
    $project = Project::find(+$id);

    if (!$project) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    if ($this->user?->isAdmin() || $project->students()->has($this->user?->student())) {
      $project->appends(["owned", "students"]);
      $project->students->appends("name");
    }

    return $project;
  }

  public function create() {
    if (!$this->user?->isAdmin() && !$this->user?->isStudent()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    if ($this->user?->isStudent()) {
      $student = $this->user?->student();

      return [
        "groups" => $student->groups(),
        "subjects" => $this->request->has("group_id") ? $student->subjects(true)->where("group_id", +($this->request->group_id ?? 0))->get()->unique() : [],
      ];
    }

    return [
      "groups" => Group::get(),
      "subjects" => Subject::get(),
    ];
  }

  public function store() {
    if (!$this->user?->isAdmin() && !$this->user?->isStudent()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $project = (new Project($this->request->only([
      "name",
      "description",
      "group_id",
      "subject_id",
    ])))->save();

    $project->attach(Student::class, $this->user->student()->id);

    return $project;
  }

  public function update($id) {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $project = Project::find(+$id);

    if (!$project) {
      http_response_code(400);
      return ["error" => true, "message" => "Project doesn't exist."];
    }

    if (!$this->user?->isAdmin() && !$project->students()->has($student = $this->user?->student())) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $project->fill($this->request->only([
      "name",
      "description",
      "group_id",
      "subject_id",
    ]));

    $students = $this->request->students ?? [];

    if ($this->user?->isStudent() && !in_array($student->id, $students)) {
      $students[] = $student->id;
    }

    $project->sync(Student::class, $students);
    $project->save()->appends("students");
    $project->students->appends("name");

    return $project;
  }
}
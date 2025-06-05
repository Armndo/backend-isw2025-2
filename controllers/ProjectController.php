<?php
namespace Controllers;

use Core\Controller;
use Models\Project;
use Models\Student;

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

    return $project;
  }

  public function store() {
    if (!$this->user?->isAdmin() && $this->user?->type !== "student") {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $project = new Project($this->request->only([
      "name",
      "description",
      "group_id",
      "subject_id",
    ]))->save();

    $project->attach(Student::class, $this->user->student()->id);

    return $project;
  }

  public function update($id) {
    $project = Project::find(+$id);

    if (!$project) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $project->fill($this->request->only([
      "name",
      "description",
      "group_id",
      "subject_id",
    ]));

    return $project->save();
  }
}
<?php
namespace Controllers;

use Core\Controller;
use Models\Project;

class ProjectController extends Controller {
  public function index() {
    return Project::get()->toJson();
  }

  public function view($id) {
    $project = Project::find(+$id);

    if (!$project) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    return $project->toJson();
  }

  public function store() {
    $project = new Project($this->request->only([
      "name",
      "description",
      "group_id",
      "subject_id",
    ]));

    return $project->save();
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
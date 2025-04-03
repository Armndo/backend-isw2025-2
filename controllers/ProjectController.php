<?php
namespace Controllers;

use Models\Project;

class ProjectController {
  public function index() {
    return Project::get()->toJson();
  }

  public function view($id) {
    return Project::find($id)->toJson();
  }
}
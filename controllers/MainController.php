<?php
namespace Controllers;

use Core\Controller;
use Models\Group;
use Models\Project;
use Models\Student;
use Models\Subject;

class MainController extends Controller {
  private function student() {
    $student = $this->user?->student();
    $projects = $student->projects();
    $groups = $student->groups();
    $subjects = $student->subjects();

    return [
      "user" => $this->user,
      "student" => $student,
      "projects" => $projects,
      "groups" => $groups,
      "subjects" => $subjects,
    ];
  }

  private function admin() {
    return [
      "user" => $this->user,
      "projects" => Project::get(),
      "groups" => Group::get(),
      "subjects" => Subject::get(),
      "students" => Student::get(),
    ];
  }

  public function main() {
    if ($this->user?->isStudent()) {
      return $this->student();
    }

    if ($this->user?->isAdmin()) {
      return $this->admin();
    }

    http_response_code(401);
    return ["error" => true, "message" => "Unauthenticated."];
  }
}
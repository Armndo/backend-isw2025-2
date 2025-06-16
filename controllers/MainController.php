<?php
namespace Controllers;

use Core\Controller;

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

  public function main() {
    if ($this->user?->isStudent()) {
      return $this->student();
    }

    http_response_code(401);
    return ["error" => true, "message" => "Unauthenticated."];
  }
}
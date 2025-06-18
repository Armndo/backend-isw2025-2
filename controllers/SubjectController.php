<?php
namespace Controllers;

use Core\Controller;
use Models\Major;
use Models\Subject;

class SubjectController extends Controller {
  public function index() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Subject::get();
  }

  public function view($id) {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $subject = Subject::find(+$id);

    if (!$subject) {
      http_response_code(400);
      return ["error" => true, "message" => "Subject doesn't exist."];
    }

    if ($this->user?->isAdmin()) {
      $subject->makeVisible("major_id");
    }

    return $subject;
  }

  public function create() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return [
      "majors" => Major::get(),
    ];
  }

  public function store() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return (new Subject($this->request->only([
      "name",
      "major_id",
    ])))->save();
  }

  public function update($id) {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $subject = Subject::find(+$id);

    if (!$subject) {
      http_response_code(400);
      return ["error" => true, "message" => "Subject doesn't exist."];
    }

    return $subject->fill($this->request->only([
      "name",
      "major_id",
    ]))->save();
  }
}
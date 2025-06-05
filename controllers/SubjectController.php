<?php
namespace Controllers;

use Core\Controller;
use Models\Subject;

class SubjectController extends Controller {
  public function index() {
    return Subject::get()->toJson();
  }

  public function view($id) {
    $subject = Subject::find(+$id);

    if (!$subject) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    return $subject->toJson();
  }

  public function store() {
    $subject = new Subject($this->request->only([
      "name",
      "major_id",
    ]));

    return $subject->save();
  }

  public function update($id) {
    $subject = Subject::find(+$id);

    if (!$subject) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $subject->fill($this->request->only([
      "name",
      "major_id",
    ]));

    return $subject->save();
  }
}
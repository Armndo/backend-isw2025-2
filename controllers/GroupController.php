<?php
namespace Controllers;

use Core\Controller;
use Models\Group;

class GroupController extends Controller {
  public function index() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Group::get();
  }

  public function view($id) {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $group = Group::find(+$id);

    if (!$group) {
      http_response_code(400);
      return ["error" => true, "message" => "Group doesn't exist."];
    }

    if ($group->students()->has($student = $this->user?->student())) {
      $group->subjects = $student->subjects(true)->where("group_id", $group->id)->get()->unique()->appends("students");

      foreach ($group->subjects as &$subject) {
        $subject->students->appends("name");
      }

      return $group;
    }

    foreach($group->appends("subjects")->subjects as &$subject) {
      $subject->appends("students");
      $subject->students->appends("name");
    }

    return $group;
  }

  public function store() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    (new Group($this->request->only([
      "name",
      "shift_id",
      "major_id",
    ])))->save();

    return "Ok";
  }

  public function update($id) {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $group = Group::find(+$id);

    if (!$group) {
      http_response_code(400);
      return ["error" => true, "message" => "Group doesn't exist."];
    }

    $group->fill($this->request->only([
      "name",
      "group_id",
      "major_id",
    ]))->save();

    return "Ok";
  }
}
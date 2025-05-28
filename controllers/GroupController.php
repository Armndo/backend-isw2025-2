<?php
namespace Controllers;

use Core\Controller;
use Models\Group;

class GroupController extends Controller {
  public function index() {
    return Group::get()->toJson();
  }

  public function view($id) {
    $group = Group::find(+$id);

    if (!$group) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    return $group->toJson();
  }

  public function store() {
    $group = new Group($this->request->only([
      "name",
      "shift_id",
      "major_id",
    ]));

    return $group->save();
  }

  public function update($id) {
    $group = Group::find(+$id);

    if (!$group) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $group->fill($this->request->only([
      "name",
      "group_id",
      "major_id",
    ]));

    return $group->save();
  }
}
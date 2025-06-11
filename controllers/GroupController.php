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

    return $group;
  }

  public function store() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    new Group($this->request->only([
      "name",
      "shift_id",
      "major_id",
    ]))->save();

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
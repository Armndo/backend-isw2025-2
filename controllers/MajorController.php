<?php
namespace Controllers;

use Core\Controller;
use Models\Major;

class MajorController extends Controller {
  public function index() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Major::get();
  }

  public function view($id) {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $major = Major::find(+$id);

    if (!$major) {
      http_response_code(400);
      return ["error" => true, "message" => "Major doesn't exist."];
    }

    return $major;
  }

  public function store() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    new Major($this->request->only([
      "name",
    ]))->save();

    return "Ok";
  }

  public function update($id) {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $major = Major::find(+$id);

    if (!$major) {
      http_response_code(400);
      return ["error" => true, "message" => "Major doesn't exist."];
    }

    $major->fill($this->request->only([
      "name",
    ]))->save();

    return "Ok";
  }
}
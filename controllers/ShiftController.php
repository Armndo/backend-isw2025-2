<?php
namespace Controllers;

use Core\Controller;
use Models\Shift;

class ShiftController extends Controller {
  public function index() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return Shift::get();
  }

  public function view($id) {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $shift = Shift::find(+$id);

    if (!$shift) {
      http_response_code(400);
      return ["error" => true, "message" => "Shift doesn't exist."];
    }

    return $shift;
  }

  public function store() {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    new Shift($this->request->only([
      "name",
    ]))->save();

    return "Ok";
  }

  public function update($id) {
    if (!$this->user?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $shift = Shift::find(+$id);

    if (!$shift) {
      http_response_code(400);
      return ["error" => true, "message" => "Shift doesn't exist."];
    }

    $shift->fill($this->request->only([
      "name",
    ]))->save();

    return "Ok";
  }
}
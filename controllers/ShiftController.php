<?php
namespace Controllers;

use Core\Controller;
use Models\Shift;

class ShiftController extends Controller {
  public function index() {
    return Shift::get()->toJson();
  }

  public function view($id) {
    $shift = Shift::find(+$id);

    if (!$shift) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    return $shift->toJson();
  }

  public function store() {
    $shift = new Shift($this->request->only([
      "name",
    ]));

    return $shift->save();
  }

  public function update($id) {
    $shift = Shift::find(+$id);

    if (!$shift) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $shift->fill($this->request->only([
      "name",
    ]));

    return $shift->save();
  }
}
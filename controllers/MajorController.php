<?php
namespace Controllers;

use Core\Controller;
use Models\Major;

class MajorController extends Controller {
  public function index() {
    return Major::get()->toJson();
  }

  public function view($id) {
    $major = Major::find(+$id);

    if (!$major) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    return $major->toJson();
  }

  public function store() {
    $major = new Major($this->request->only([
      "name",
    ]));

    return $major->save();
  }

  public function update($id) {
    $major = Major::find(+$id);

    if (!$major) {
      http_response_code(400);
      print("invalid request");
      exit();
    }

    $major->fill($this->request->only([
      "name",
    ]));

    return $major->save();
  }
}
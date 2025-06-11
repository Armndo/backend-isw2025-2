<?php

namespace Controllers;

use Models\Sign;
use Core\Controller;

class SignController extends Controller
{
  public function index() {
    return Sign::get()->toJson();
  }

  public function view($id) {
    return Sign::find($id)->toJson();
  }

  public function update($id) {
    $sign = Sign::find($id);

    if (!$sign) {
      http_response_code(500);
      return ["error" => true, "message" => "Sign doesn't exist."];
    }

    $sign->fill($this->request->only([
      "sign",
    ]));
    $sign->save();
  }

  public function store() {
    return new Sign($this->request->only([
      "sign",
      "printed",
      "project_id",
    ]));
  }

  public function print($id) {
    if (!$this->session?->user()?->isAdmin()) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $sign = Sign::find($id);

    if (!$sign) {
      http_response_code(500);
      return ["error" => true, "message" => "Sign doesn't exist."];
    }

    if ($sign->printed) {
      http_response_code(500);
      return ["error" => true, "message" => "Sign already printed."];
    }

    $sign->printed = true;
    $sign->save();

    return "Ok";
  }
}

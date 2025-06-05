<?php
namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Utils;
use Models\User;

class UserController extends Controller {
  public function login() {
    $user = User::check($this->request->only(["email", "password"]));
    
    if ($user === null) {
      http_response_code(401);
      return json_encode(["error" => true, "message" => "Unauthorized."]);
    }

    foreach(Session::where("user_id", $user->id)->where("expired", false)->get() as $session) {
      $session->expired = true;
      $session->save();
    }

    return (new Session(["user_id" => $user->id, "token" => Utils::token()]))->save()->toJson();
  }

  public function logout() {
    if (!$this->session) {
      http_response_code(401);
      return json_encode(["error" => true, "message" => "Unauthorized."]);
    }

    $this->session->expired = true;
    $this->session->save();

    return "Ok";
  }
}
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
      return ["error" => true, "message" => "Unauthorized."];
    }

    Session::where("user_id", $user->id)->where("expired", false)->update([ "expired" => true ]);

    return (new Session(["user_id" => $user->id, "token" => Utils::token()]))->save();
  }

  public function logout() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $this->session->expired = true;
    $this->session->save();

    return "Ok";
  }

  public function logged() {
    if (!$this->session) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    return "Ok";
  }
}
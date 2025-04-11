<?php
namespace Controllers;

use Core\Controller;
use Models\User;

class UserController extends Controller {
  public function index() {
    return User::get()->toJson();
  }

  public function view($id) {
    $user = User::find($id);

    return $user->projects()->toJson();
    return $user->toJson();
  }
}
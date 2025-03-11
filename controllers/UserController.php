<?php
namespace Controllers;

use Models\User;

class UserController {
  public function index() {
    return User::get();
  }

  public function view($id) {
    return User::find($id);
  }
}
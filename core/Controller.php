<?php
namespace Core;

use Models\User;

class Controller {
  public Request $request;
  public ?Session $session;
  public ?User $user;

  public function __construct() {
    $this->request = new Request();
    $this->session = Session::check($this->request);
    $this->user = $this->session?->user();
  }
}
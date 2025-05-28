<?php
namespace Core;

class Controller {
  public Request $request;
  public ?Session $session;

  public function __construct() {
    $this->request = new Request();
    $this->session = Session::check($this->request);
  }
}
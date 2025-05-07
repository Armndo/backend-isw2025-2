<?php
namespace Controllers;

use Models\Place;
use Core\Controller;

class PlaceController extends Controller {
  public function index() {
    return Place::get()->toJson();
  }

  public function view($id) {
    return Place::find($id)->toJson();
  }
}
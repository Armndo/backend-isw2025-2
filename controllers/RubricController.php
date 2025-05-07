<?php
namespace Controllers;

use Models\Controller;
use Models\Rubric;

class RubricController extends Controller
{
    public function index()
    {
        return Rubric::get()->toJson();
    }

    public function view($id)
    {
        return Rubric::find($id)->toJson();
    }

    public function store()
    {
        $rubric = new Rubric($this->request->only([
            "id",
            "calification",
        ]));
    }
}
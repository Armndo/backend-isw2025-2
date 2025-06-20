<?php
// Revisar Jury.php
namespace Controllers;

use Models\Controller;
use Models\Jury;

class JuryController extends Controller
{
    public function index()
    {
        return Jury::get()->toJson();
    }

    public function view($id)
    {
        return Jury::find($id)->toJson();
    }

    public function store()
    {
        $jury = new Jury($this->request->only([
            "id",
        ]));
    }
}
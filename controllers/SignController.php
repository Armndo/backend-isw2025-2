<?php
namespace Controllers;

use Models\Sign;
use Core\Controller;

class SignController extends Controller
{
    public function index(){
        return Sign::get()->toJson();
    }

    public function view($id){
        return Sign::find($id)->toJson();
    }

    public function store()
    {
        $sign = new Sign($this->request->only([
            "id",
            "sign",
            "printed"
        ]));
    }
}
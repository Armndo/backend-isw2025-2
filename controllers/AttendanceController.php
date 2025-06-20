<?php
namespace Controllers;

use Models\Controller;
use Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        return Attendance::get()->toJson();
    }

    public function view($id)
    {
        return Attendance::find($id)->toJson();
    }

    public function store()
    {
        $attendance = new Attendance($this->request->only([
            "id",
            "satisfaction",
            "observations",
        ]));
    }
}
<?php
require __DIR__ . '/../vendor/autoload.php';

$env = parse_ini_file(__DIR__ . "/../.env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
}

header("Access-Control-Allow-Origin: *");

use Controllers\GroupController;
use Controllers\MajorController;
use Controllers\ProjectController;
use Controllers\ShiftController;
use Controllers\StudentController;
use Controllers\SubjectController;
use Controllers\UserController;
use Core\Model;
use Core\Router;
use Models\Group;
use Models\Major;
use Models\Shift;

Router::get("/majors", [MajorController::class, "index"]);
Router::post("/majors", [MajorController::class, "store"]);
Router::get("/majors/{id}", [MajorController::class, "view"]);
Router::post("/majors/{id}", [MajorController::class, "update"]);

Router::get("/shifts", [ShiftController::class, "index"]);
Router::post("/shifts", [ShiftController::class, "store"]);
Router::get("/shifts/{id}", [ShiftController::class, "view"]);
Router::post("/shifts/{id}", [ShiftController::class, "update"]);

Router::get("/groups", [GroupController::class, "index"]);
Router::post("/groups", [GroupController::class, "store"]);
Router::get("/groups/{id}", [GroupController::class, "view"]);
Router::post("/groups/{id}", [GroupController::class, "update"]);

Router::get("/subjects", [SubjectController::class, "index"]);
Router::post("/subjects", [SubjectController::class, "store"]);
Router::get("/subjects/{id}", [SubjectController::class, "view"]);
Router::post("/subjects/{id}", [SubjectController::class, "update"]);

Router::get("/users", [UserController::class, "index"]);
Router::get("/users/{id}", [UserController::class, "view"]);

Router::get("/projects", [ProjectController::class, "index"]);
Router::post("/projects", [ProjectController::class, "store"]);
Router::get("/projects/{id}", [ProjectController::class, "view"]);
Router::post("/projects/{id}", [ProjectController::class, "update"]);

Router::get("/students", [StudentController::class, "index"]);
Router::post("/students", [StudentController::class, "store"]);
Router::get("/students/{id}", [StudentController::class, "view"]);
Router::post("/students/{id}", [StudentController::class, "update"]);

Router::get("/", function() {
  // print("lmao\n");

  // $tmp = Group::select("shifts.*")->join("majors", "majors.id", "groups.major_id")->join("shifts", "shifts.id", "groups.shift_id")->first(Shift::class);
  $tmp = Group::select("shifts.*")->join("majors", "majors.id", "groups.major_id")->join("shifts", "shifts.id", "groups.shift_id")->find(2);
  // var_dump($tmp);

  return $tmp;
});

print(Router::resolve());
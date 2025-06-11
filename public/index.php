<?php
require __DIR__ . '/../vendor/autoload.php';

$env = parse_ini_file(__DIR__ . "/../.env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
}

use Controllers\GroupController;
use Controllers\MajorController;
use Controllers\ProjectController;
use Controllers\ShiftController;
use Controllers\StudentController;
use Controllers\SubjectController;
use Controllers\TeacherController;
use Controllers\UserController;
use Core\Router;

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

Router::post("/login", [UserController::class, "login"]);
Router::post("/logout", [UserController::class, "logout"]);

Router::get("/students", [StudentController::class, "index"]);
Router::post("/students", [StudentController::class, "store"]);
Router::get("/students/{id}", [StudentController::class, "view"]);
Router::post("/students/{id}", [StudentController::class, "update"]);
Router::post("/students/{id}/enroll", [StudentController::class, "enroll"]);

Router::get("/teachers", [TeacherController::class, "index"]);
Router::post("/teachers", [TeacherController::class, "store"]);
Router::get("/teachers/{id}", [TeacherController::class, "view"]);
Router::post("/teachers/{id}", [TeacherController::class, "update"]);
Router::post("/teachers/{id}/teach", [TeacherController::class, "teach"]);
Router::get("/teachers/{id}/subjects", [TeacherController::class, "subjects"]);
Router::get("/teachers/{id}/groups", [TeacherController::class, "groups"]);

print(Router::resolve());
<?php
require __DIR__ . '/../vendor/autoload.php';

$env = parse_ini_file(__DIR__ . "/../.env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
}

use Controllers\ProjectController;
use Controllers\UserController;
use Core\Router;

Router::get("/users", [UserController::class, "index"]);
Router::get("/users/{id}", [UserController::class, "view"]);
Router::get("/projects", [ProjectController::class, "index"]);
Router::get("/projects/{id}", [ProjectController::class, "view"]);

print(Router::resolve());
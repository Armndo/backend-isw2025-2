<?php
include_once("models/User.php");
header('Content-Type: application/json; charset=utf-8');

$env = parse_ini_file(".env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
  $_ENV[$key] = $value;
  $_SERVER[$key] = $value;
}

print(json_encode(User::get()->toJson()));
<?php
require __DIR__ . '/../vendor/autoload.php';

use Models\User;

header('Content-Type: application/json; charset=utf-8');

$env = parse_ini_file(__DIR__ . "/../.env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
}

print(User::get());
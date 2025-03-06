<?php
spl_autoload_register(function ($class) {
  $parts = explode("\\", $class);
  $classname = array_pop($parts);
  $paths = implode("/", array_map(function($path) { return strtolower($path); }, $parts));
  $file = "$paths/$classname.php";

  print("\n");
  print("$class\n");
  print("$classname\n");
  print("$file\n");
  var_dump(file_exists($file));
  // print_r(get_declared_interfaces());
  // interface_exists()
  // exit();
  print("\n");

  if (file_exists($file) && (!class_exists($class) && !interface_exists($classname))
  //  and !in_array($classname)
  ) {
    require $file;
  }
});

use Models\User;

header('Content-Type: application/json; charset=utf-8');

$env = parse_ini_file(".env");
foreach ($env as $key => $value) {
  putenv("$key=$value");
}

// print(User::get()->toJson());
// var_dump(User::where("id", 1));
// print_r(User::where("age", ">", 20)->where("lastname", "IS NOT", null)->get()->toJson());
// print_r(User::where("name", "ilike", "e%")->get()->toJson());
// print_r(User::find(1)->toJson());
// print_r(User::find(1)->toJson());
// print(User::find(1));
print(User::where("lastname", "IS", null)->orderBy("id", "desc")->get());
// print(User::whereRaw("name like '%mando'")->get());

// $user = new User(["name" => "dorime"]);
// print($user->save());
print($user = User::find(1));
$user->age = 10;
print($user->save());
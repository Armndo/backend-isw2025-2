<?php
namespace Core;

class Router {
  private static array $routes = [];

  public static function get($uri, callable | array $callback) {
    static::$routes["GET"][$uri] = $callback;
  }

  public static function post($uri, callable | array $callback) {
    static::$routes["POST"][$uri] = $callback;
  }

  public static function resolve(): mixed {
      $method = $_SERVER['REQUEST_METHOD'];
      $path = $_SERVER['REQUEST_URI'] ?? '/';
      $path = explode('?', $path)[0];

      $callback = static::$routes[$method][$path] ?? null;

      if ($callback === null) {
          http_response_code(404);
          return "404 Not Found";
      }

      if (is_array($callback)) {
          [$classname, $function] = $callback;
          $controller = new $classname();
          return $controller->$function();
      }

      return $callback();
  }
}
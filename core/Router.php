<?php
namespace Core;

use Exception;
use Throwable;

class Router {
  private static array $routes = [];

  private static function addRoute(string $method, string $path, callable|array $callback): void {
    $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
    $pattern = "#^$pattern$#";

    static::$routes[$method][$pattern] = $callback;
  }

  public static function get($uri, callable|array $callback) {
    static::addRoute("GET", $uri, $callback);
  }

  public static function post($uri, callable|array $callback) {
    static::addRoute("POST", $uri, $callback);
  }

  public static function resolve(): mixed {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $path = explode('?', $path)[0];
    $resolved = null;

    header('Content-Type: application/json; charset=utf-8');
    header("Access-Control-Allow-Origin: " . getenv("CORS_DOMAIN"));
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if ($method === 'OPTIONS') {    
      return 0;    
    }

    foreach (static::$routes[$method] ?? [] as $pattern => $callback) {
      if (preg_match($pattern, $path, $matches)) {
        $params = array_filter(
          $matches, 
          fn($key) => !is_numeric($key), 
          ARRAY_FILTER_USE_KEY
        );

        try {
          if (is_array($callback)) {
            [$class, $method] = $callback;
            $controller = new $class();
            $resolved = $controller->$method(...$params);
          } else {
            $resolved = $callback(...$params);
          }

          $resolved = Utils::serialize($resolved);
        } catch (Exception|Throwable $e) {
          http_response_code(500);
          return json_encode(["error" => true, "message" => getenv("DEBUG") ? $e->getMessage() : "Server error."]);
        }

        return json_encode($resolved, JSON_PRETTY_PRINT);
      }
    }

    http_response_code(404);
    return json_encode("404 Not Found");
  }
}
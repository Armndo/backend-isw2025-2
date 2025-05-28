<?php
namespace Core;

use ArrayObject;

class Request extends ArrayObject {
  private $attributes = [];
  private $headers = [];
  private $files = [];

  public function __construct() {
    foreach (getallheaders() as $header => $value) {
      if ($header === "Content-Type") {
        $value = explode(";", $value)[0];
      }

      $this->headers[$header] = $value;
    }

    $this->fillAttributes($this->headers["Content-Type"] ?? null);
  }

  public function __get($name) {
    return $this->attributes[$name] ?? null;
  }

  private function fillAttributes($type) {
    switch ($type) {
      case "multipart/form-data":
        foreach ($_POST as $key => $value) {
          $this->attributes[$key] = $value;
        }

        foreach ($_FILES as $key => $value) {
          $this->attributes[$key] = $value;
          $this->files[$key] = $value;
        }

        break;

      case "application/json":
        $this->attributes = $this->jsonAttributes();
        break;

      case "application/x-www-form-urlencoded":
      case null:
        break;

      default:
        http_response_code(400);
        print("invalid content type");
        exit();
    }
  }

  private function jsonAttributes() {
    $json = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      http_response_code(400);
      print("invalid JSON");
      exit();
    }

    return $json;
  }

  public function headers() {
    return $this->headers;
  }

  public function files() {
    return $this->files;
  }

  public function all() {
    return $this->attributes;
  }

  public function exists(string $attribute) {
    return isset($this->attributes[$attribute]);
  }

  public function only(array $attributes) {
    $res = [];

    foreach ($attributes as $attribute) {
      if (isset($this->attributes[$attribute])) {
        $res[$attribute] = $this->attributes[$attribute];
      }
    }

    return $res;
  }
}
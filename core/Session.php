<?php
namespace Core;

use Models\User;

class Session extends Model {
  protected $fillable = [
    "user_id",
    "token",
    "expired",
  ];

  protected $hidden = [
    "id",
    "user_id",
    "expired",
  ];

  public static function check(Request $request): ?Session {
    $auth = $request->headers()["Authorization"] ?? "";

    if (str_contains($auth, "Bearer ")) {
      $auth = explode("Bearer ", $auth)[1];
    }

    $token = trim($auth);

    return Session::where("token", $token)->where("expired", false)->first();
  }

  public function user(): User {
    return $this->belongs(User::class);
  }
}
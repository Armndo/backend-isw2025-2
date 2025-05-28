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
    $token = trim($request->headers()["Authorization"] ?? "");

    return Session::where("token", $token)->where("expired", false)->first();
  }

  public function user() {
    return $this->belongs(User::class);
  }
}
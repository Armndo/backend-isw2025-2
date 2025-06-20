<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\Project;
use Models\Visit;
use Models\Visitor;

class VisitorController extends Controller {
  private function create() {
    return (new Visitor([
      "id" => Utils::token(40),
    ]))->save();
  }

  public function visit() {
    $visitor = Visitor::find($this->request?->visitor_id ?? "");

    if (!$visitor) {
      $visitor = $this->create();
    }

    if ($this->request->has("visit_id")) {
      $visit = Visit::where("id", +(is_numeric($this->request?->visit_id) ? $this->request?->visit_id : 0))->whereRaw("(comment IS NULL AND rating IS NULL)")->first();
      $visit = Visit::find($visit?->id ?? 0);

      if ($visit) {
        return [
          "visit" => $visit->fill(["counter" => $visit->counter + 1])->save()->makeVisible(["id", "visitor_id"]),
          "project" => $visit->project(),
        ];
      }
    }

    $project = Project::find(+($this->request?->project_id ?? 0));

    if (!$project) {
      http_response_code(400);
      return ["error" => true, "message" => "Project doesn't exist."];
    }

    return [
      "visit" => (new Visit([
        "visitor_id" => $visitor->id,
        "project_id" => $project->id,
      ]))->save()->makeVisible(["id", "visitor_id"]),
      "project" => $project,
    ];
  }

  public function fill() {
    $visit = Visit::find(+(is_numeric($this->request?->visit_id) ? $this->request?->visit_id : 0));

    if (!$visit) {
      http_response_code(404);
      return ["error" => true, "message" => "Visit doesn't exist."];
    }

    $visitor = Visitor::find($this->request?->visitor_id ?? "");

    if (!$visitor) {
      http_response_code(400);
      return ["error" => true, "message" => "Visitor doesn't exist."];
    }

    if ($visitor->id !== $visit->visitor_id) {
      http_response_code(401);
      return ["error" => true, "message" => "Unauthorized."];
    }

    $project = $visit->project();

    return [
      "visit" => $visit->fill($this->request->only([
        "comment",
        "rating",
      ]))->save()->makeVisible("id"),
      "project" => $project,
    ];
  }
}
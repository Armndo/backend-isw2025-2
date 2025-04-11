<?php
namespace Core;

use PDO;
use PDOException;

class Query {
  private $instance;
  private $wheres = [];
  private $joins = [];
  private $orders = [];
  private $selects = [ "*" ];
  private $limit = null;

  public function __construct(Model $instance) {
    $this->instance = $instance;
  }

  public function select(...$selects): self {
    if (sizeof($selects) === 1 && is_array($selects[0])) {
      $selects = $selects[0];
    }

    $this->selects = $selects;

    return $this;
  }

  public function where(...$conditions): self {
    if (in_array(sizeof($conditions), [2, 3]) && array_reduce($conditions, function ($a, $b) { return $a && gettype($b) !== "array"; }, true)) {
      $this->wheres[] = Utils::where($conditions);
    } else if(sizeof($conditions) === 1) {
      $this->wheres = [...$this->wheres, ...Utils::wheres($conditions[0])];
    }

    return $this;
  }

  public function join(string $table, string $first, string $second): self {
    $this->joins[] = (
      "\"$table\" on " .
      implode(".", array_map(function($item) {
        return "\"$item\"";
      }, explode(".", $first))) . " = " .
      implode(".", array_map(function($item) {
        return "\"$item\"";
      }, explode(".", $second)))
    );

    return $this;
  }

  public function orderBy($field, $direction = "ASC"): self {
    $this->orders[] = [$field, $direction];

    return $this;
  }

  public function whereRaw(string $raw): self {
    $this->wheres[] = $raw;

    return $this;
  }

  private function resolve($exec = false): string {
    $table = $this->instance->getTable();
    $fields = $this->instance->getFields();

    if (!$exec) {
      $selects = Utils::selects($this->selects);
      $where = Utils::wheres($this->wheres, true);
      $orderBy = Utils::orders($this->orders);
      $limit = $this->limit === null ? "" : " LIMIT $this->limit";
      $joins = sizeof($this->joins) === 0 ? "" : " JOIN " . implode(" JOIN ", $this->joins);

      // print_r("SELECT $selects FROM \"$table\"$joins$where$orderBy$limit\n");
      return "SELECT $selects FROM \"$table\"$joins$where$orderBy$limit";
    }

    $identifier = $this->instance->getIdentifier();
    $appends = $this->instance->getAppends();

    if (isset($fields[$identifier]) && $this->instance->getStored()) {
      $values = Utils::values($fields, $appends, true, $identifier);
      $id = $fields[$identifier];

      if (is_string($id)) {
        $id = "'$id'";
      }

      print_r("UPDATE \"$table\" SET $values WHERE \"$identifier\" = $id RETURNING *\n");
      return "UPDATE \"$table\" SET $values WHERE \"$identifier\" = $id RETURNING *";
    }

    $values = Utils::values($fields, $appends);
    $fields = Utils::fields($fields, $appends, $identifier);

    print_r("INSERT INTO \"$table\" ($fields) VALUES ($values) RETURNING *\n");
    return "INSERT INTO \"$table\" ($fields) VALUES ($values) RETURNING *";
  }

  public function find(int | string $id): Model | null {
    $this->wheres[] = Utils::where([$this->instance->getIdentifier(), $id]);
    $result = $this->run($this->resolve());

    if (sizeof($result) < 1) {
      return null;
    }

    return $this->instance->fill($result[0], true, true);
  }

  public function get(): Collection {
    return new Collection(
      array_map(function ($fields) {
        return new (get_class($this->instance))($fields, true, true);
      }, $this->run($this->resolve()))
    );
  }

  public function first(): Model {
    $this->limit = 1;

    return new (get_class($this->instance))($this->run($this->resolve())[0], true, true);
  }

  public function save(): Model {
    return $this->instance->fill($this->run($this->resolve(true), true)[0], true, true);
  }

  private function run($sql): array {
    try {
      $conn = (new Connection())->getConnection();
      $query = $conn->prepare($sql);
      $query->execute();

      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
    } finally {
      $conn = null;
    }
  }
}
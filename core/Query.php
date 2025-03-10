<?php
namespace Core;

use PDO;
use PDOException;

class Query {
  // public const array operators = [
  //   "=",
  //   ">=",
  //   "<=",
  //   ">",
  //   "<",
  // ];
  private $instance;
  private $wheres = [];
  private $orders = [];
  private $selects = [ "*" ];

  public function __construct(Model $instance) {
    $this->instance = $instance;
  }

  public function where(...$conditions): self {
    if (in_array(sizeof($conditions), [2, 3]) && array_reduce($conditions, function ($a, $b) { return $a && gettype($b) !== "array"; }, true)) {
      $this->wheres[] = Utils::where($conditions);
    } else if(sizeof($conditions) === 1) {
      $this->wheres = [...$this->wheres, ...Utils::wheres($conditions[0])];
    }

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
      $selects = implode(", ", $this->selects);
      $where = Utils::wheres($this->wheres, true);
      $orderBy = Utils::orders($this->orders);

      return "SELECT $selects FROM $table$where$orderBy";
    }

    $identifier = $this->instance->getIdentifier();
    $appends = $this->instance->getAppends();

    if (isset($fields[$identifier])) {
      $values = Utils::values($fields, $appends, true, $identifier);
      $id = $fields[$identifier];

      return "UPDATE $table SET $values WHERE $identifier = $id RETURNING *";  
    }

    $values = Utils::values($fields, $appends);
    $fields = Utils::fields($fields, $appends, $identifier);

    return "INSERT INTO $table ($fields) VALUES ($values) RETURNING *";
  }

  public function find(int | string $id): Model | null {
    $this->wheres[] = Utils::where([$this->instance->getIdentifier(), +$id]);
    $result = $this->run($this->resolve());

    if (sizeof($result) < 1) {
      return null;
    }

    return $this->instance->fill($result[0], true);
  }

  public function get(): Collection {
    return new Collection(
      array_map(function ($fields) {
        return new (get_class($this->instance))($fields, true);
      }, $this->run($this->resolve()))
    );
  }

  public function save(): Model {
    return $this->instance->fill($this->run($this->resolve(true), true)[0], true);
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
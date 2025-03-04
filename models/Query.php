<?php
include_once("models/Model.php");
include_once("models/Collection.php");
include_once("utils/Utils.php");

class Query {
  // public const array operators = [
  //   "=",
  //   ">=",
  //   "<=",
  //   ">",
  //   "<",
  // ];
  private $wheres = [];
  private $orders = [];
  private $fields = [ "*" ];
  private $table;
  private $identifier;
  private $classname;

  public function __construct($classname, $table, $identifier) {
    $this->classname = $classname;
    $this->table = $table;
    $this->identifier = $identifier;
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

  // public function whereRaw(string $raw) {
  //   $this->wheres[] = $raw;

  //   return $this;
  // }

  private function resolve(): string {
    $fields = implode(", ", $this->fields);
    $where = Utils::wheres($this->wheres, true);
    $orderBy = Utils::orders($this->orders);
    $sql = "SELECT $fields FROM $this->table$where$orderBy";

    return $sql;
  }

  public function find(int | string $id): Model | null {
    $this->wheres[] = Utils::where([$this->identifier, +$id]);
    $result = $this->run($this->resolve());

    if (sizeof($result) < 1) {
      return null;
    }

    return new $this->classname($result[0], true);
  }

  public function get(): Collection {
    return new Collection(
      array_map(function ($fields) {
        return new $this->classname($fields, true);
      }, $this->run($this->resolve()))
    );
  }

  private function run($sql): array {
    try {
      $conn = (new Connection())->getConnection();
      $query = $conn->query($sql);

      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
    } finally {
      $conn = null;
    }
  }
}
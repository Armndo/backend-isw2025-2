<?php
namespace Core;

use Exception;
use PDO;
use PDOException;

class Query {
  private Model $instance;
  private ?Collection $collection = null;
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

  private function resolve(bool $exec = false, bool $count = false): ?string {
    $table = $this->instance->getTable();
    $fields = $this->instance->getFields();

    if (!$exec) {
      $selects = Utils::selects($this->selects, $count);
      $where = Utils::wheres($this->wheres, true);
      $orderBy = Utils::orders($this->orders);
      $limit = $this->limit === null ? "" : " LIMIT $this->limit";
      $joins = sizeof($this->joins) === 0 ? "" : " JOIN " . implode(" JOIN ", $this->joins);

      if (getenv("DEBUG")) {
        print_r("SELECT $selects FROM \"$table\"$joins$where$orderBy$limit\n");
      }
      
      if (!getenv("STOP_QUERIES")) {
        return "SELECT $selects FROM \"$table\"$joins$where$orderBy$limit";
      }
    }

    $identifier = $this->instance->getIdentifier();
    $appends = $this->instance->getAppends();

    if (isset($fields[$identifier]) && $this->instance->getStored()) {
      $values = Utils::values($fields, $appends, true, $identifier);
      $id = $fields[$identifier];

      if (is_string($id)) {
        $id = "'$id'";
      }

      if (getenv("DEBUG")) {
        print_r("UPDATE \"$table\" SET $values WHERE \"$identifier\" = $id RETURNING *\n");
      }
      
      if (!getenv("STOP_QUERIES")) {
        return "UPDATE \"$table\" SET $values WHERE \"$identifier\" = $id RETURNING *";
      }
    }

    $values = Utils::values($this->collection ? $this->instance->getFillable() : $fields, $appends, collection: $this->collection);
    $fields = Utils::fields($this->collection ? $this->instance->getFillable() : $fields, $appends, $this->collection);

    if (getenv("DEBUG")) {
      print_r("INSERT INTO \"$table\" ($fields) VALUES $values RETURNING *\n");
    }
    
    if (!getenv("STOP_QUERIES")) {
      return "INSERT INTO \"$table\" ($fields) VALUES $values RETURNING *";
    }

    return null;
  }

  public function find(int|string $id): ?Model {
    $this->wheres[] = Utils::where([$this->instance->getIdentifier(), $id]);
    $this->limit = 1;
    $result = $this->run($this->resolve());

    if (sizeof($result ?? []) < 1) {
      return null;
    }

    return $this->instance->fill($result[0], true, true);
  }

  public function get(?string $class = null): Collection {
    return new Collection($this->run($this->resolve()) ?? [])
    ->map(function($item) use ($class) {
      return new ($class ?? $this->instance::class)($item, true, true);
    }, false);
  }

  public function count(): int {
    return $this->run($this->resolve(count: true))[0]["count"] ?? 0;
  }

  public function first(?string $class = null): ?Model {
    $this->limit = 1;

    $object = $this->run($this->resolve());

    return sizeof($object ?? []) > 0 ? new ($class ?? $this->instance::class)($object[0], true, true) : null;
  }

  public function save(): Model {
    return $this->instance->fill(($this->run($this->resolve(true), true) ?? [[]])[0], true, true);
  }

  public function create(array $items): null|Model|Collection {
    if (sizeof($items) === 0) {
      return null;
    }

    if (is_array($items[0])) {
      $this->collection = new Collection($items)->map(function($item) {
        return new ($this->instance::class)($item);
      }, false);

      return new Collection($this->run($this->resolve(true)) ?? [])
      ->map(function($item) {
        return new ($this->instance::class)($item, true, true);
      }, false);
    }

    $this->instance->fill($items);
    return $this->instance->fill(($this->run($this->resolve(true), true) ?? [[]])[0], true, true);
  }

  public function exists(array $conditions, bool $return = false): null|bool|Model|Collection {
    foreach ($conditions as $condition) {
      $this->where(...$condition);
    }

    $count = $this->count();

    if ($return) {
      return $count > 1 ? $this->get() : $this->first();
    }

    return $count > 0;
  }

  public function attach(string $class, int|string|array $ids, ?string $table = null) {
    $instance = $this->instance;
    $instance_key = Utils::getKey($instance::class);
    $instance_id = $instance->{$instance->getIdentifier()};
    $class_key = Utils::getKey($class);

    $model = new Model();
    $model->setTable($table ?? Utils::getPivot([$instance::class, $class]));

    try {
      if (is_string($ids) || is_numeric($ids)) {
        if (Model::exists([
          [$instance_key, $instance_id],
          [$class_key, $ids],
        ], model: $model)) {
          return ;
        }

        $model->fill([
          $instance_key => $instance_id,
          $class_key => $ids,
        ], true)
        ->save();
      }

      if (!is_array($ids)) {
        return ;
      }

      foreach ($ids as $id) {
        if (
          !Model::exists([
            ...(!is_array($id) ? [[$class_key, $id]]
              : array_map(function($key, $value) {
                return [$key, $value];
              }, array_keys($id), array_values($id))
            ),
            [$instance_key, $instance_id],
          ], model: $model)
        ) {
          $model->fill([
            ...(!is_array($id) ? [$class_key => $id] : $id),
            $instance_key => $instance_id,
          ], true)
          ->save();

          continue;
        }
      }
    } catch (Exception $e) {
			print($e->getMessage() . "\n");
    }
  }

  private function run(?string $sql): ?array {
    if (!$sql) {
      return null;
    }

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
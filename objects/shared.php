<?php

class Shared {
  private $connection;
  private $table_name = "shared";

  public $id;
  public $user_id;
  public $photo_id;

  public function __construct($db) {
      $this->connection = $db;
  }

  function read_by_photo() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE photo_id=" . $this->photo_id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function read_by_user() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id=" . $this->user_id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function create() {
    if ($this->exists()) return false;

    $query = "INSERT INTO " . $this->table_name . " SET photo_id=:photo_id, user_id=:user_id";
    $stmt = $this->connection->prepare($query);

    $stmt->bindParam(":photo_id", $this->photo_id);
    $stmt->bindParam(":user_id", $this->user_id);

    return $stmt->execute();
  }

  function exists() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id=" . $this->user_id . " AND photo_id=" . $this->photo_id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }
}

?>
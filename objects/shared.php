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

  function read($photo_id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE photo_id=" . $photo_id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function create() {
    $query = "INSERT INTO " . $this->table_name . " SET photo_id=:photo_id, user_id=:user_id";
    $stmt = $this->connection->prepare($query);

    $stmt->bindParam(":photo_id", $this->photo_id);
    $stmt->bindParam(":user_id", $this->user_id);

    return $stmt->execute();
  }
}

?>
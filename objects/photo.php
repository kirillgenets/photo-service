<?php

class User {
  private $connection;
  private $table_name = "photos";
  private $default_photo_name = "Untitled";

  public $id;
  public $name;
  public $url;
  public $owner_id;
  public $users;

  public function __construct($db) {
      $this->connection = $db;
  }

  function read() {
    $query = "SELECT * FROM " . $this->table_name;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function create() {
    $query = "INSERT INTO " . $this->table_name . " SET name=:name, url=:url, owner_id=:owner_id";
    $stmt = $this->connection->prepare($query);

    $this->url = htmlspecialchars(strip_tags($this->url));
    $this->owner_id = htmlspecialchars(strip_tags($this->owner_id));
    $this->name = $this->default_photo_name;

    $stmt->bindParam(":url", $this->url);
    $stmt->bindParam(":owner_id", $this->owner_id);
    $stmt->bindParam(":name", $this->name);

    if ($stmt->execute()) {
      $id_query = "SELECT id, name, url FROM " . $this->table_name . " WHERE url=" . $this->url;
      $id_stmt = $this->connection->prepare($id_query);
      $id_stmt->execute();

      return $id_stmt->fetch(PDO::FETCH_ASSOC);
    }

    return false;
  }
}

?>
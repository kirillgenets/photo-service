<?php

class Photo {
  private $connection;
  private $table_name = "photos";
  private $default_photo_name = "Untitled";

  public $id;
  public $name;
  public $hashtags;
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

  function read_one($id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id=" . $id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function create() {
    $query = "INSERT INTO " . $this->table_name . " SET name=:name, url=:url, owner_id=:owner_id, hashtags=:hashtags";
    $stmt = $this->connection->prepare($query);

    $this->name = $this->name === '' ? $this->default_photo_name : $this->name;

    $stmt->bindParam(":url", $this->url);
    $stmt->bindParam(":owner_id", $this->owner_id);
    $stmt->bindParam(":name", $this->name);
    $stmt->bindParam(":hashtags", $this->hashtags);

    if ($stmt->execute()) {
      $id_query = "SELECT id, name, url, hashtags FROM " . $this->table_name . " WHERE url='" . $this->url . "'";
      $id_stmt = $this->connection->prepare($id_query);
      $id_stmt->execute();

      return $id_stmt->fetch(PDO::FETCH_ASSOC);
    }

    return false;
  }

  function is_deletable($id, $owner_id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id=" . $id . " AND owner_id=" . $owner_id;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
  }

  function delete($id, $owner_id) {
    $query = "DELETE FROM " . $this->table_name . " WHERE id=" . $id . " AND owner_id=" . $owner_id;
    $stmt = $this->connection->prepare($query);
    $is_auth = $this->is_deletable($id, $owner_id);
    $stmt->execute();

    return $is_auth;
  }
}

?>
<?php

class User {
  private $connection;
  private $table_name = "users";

  public $id;
  public $first_name;
  public $surname;
  public $phone;

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
    $query = "INSERT INTO " . $this->table_name . " SET first_name=:first_name, surname=:surname, phone=:phone";
    $stmt = $this->connection->prepare($query);

    $this->first_name = htmlspecialchars(strip_tags($this->first_name));
    $this->surname = htmlspecialchars(strip_tags($this->surname));
    $this->phone = htmlspecialchars(strip_tags($this->phone));

    $stmt->bindParam(":first_name", $this->first_name);
    $stmt->bindParam(":surname", $this->surname);
    $stmt->bindParam(":phone", $this->phone);

    if ($stmt->execute()) {
        return true;
    }

    return false;
  }
}

?>
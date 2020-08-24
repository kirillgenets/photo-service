<?php

class User {
  private $connection;
  private $table_name = "users";

  public $id;
  public $first_name;
  public $surname;
  public $phone;
  public $password;

  public function __construct($db) {
      $this->connection = $db;
  }

  function get_id() {
    $id_query = "SELECT id FROM " . $this->table_name . " WHERE phone=" . $this->phone;
    $id_stmt = $this->connection->prepare($id_query);
    $id_stmt->execute();

    return $id_stmt->fetch(PDO::FETCH_ASSOC)['id'];
  }

  function read() {
    $query = "SELECT * FROM " . $this->table_name;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  function create() {
    $query = "INSERT INTO " . $this->table_name . " SET first_name=:first_name, surname=:surname, phone=:phone, password=:password";
    $stmt = $this->connection->prepare($query);

    $this->first_name = htmlspecialchars(strip_tags($this->first_name));
    $this->surname = htmlspecialchars(strip_tags($this->surname));
    $this->phone = htmlspecialchars(strip_tags($this->phone));
    $this->password = htmlspecialchars(strip_tags($this->password));

    $stmt->bindParam(":first_name", $this->first_name);
    $stmt->bindParam(":surname", $this->surname);
    $stmt->bindParam(":phone", $this->phone);
    $stmt->bindParam(":password", $this->password);

    if ($stmt->execute()) {
      return $this->get_id();
    }

    return false;
  }

  function login() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE phone=" . $this->phone;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return password_verify($this->password, $result['password']) ? $this->get_id() : false;
  }

  function exists($phone) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE phone=" . $phone;
    $stmt = $this->connection->prepare($query);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }
}

?>
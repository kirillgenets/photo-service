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
}

?>
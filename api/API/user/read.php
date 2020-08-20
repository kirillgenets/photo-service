<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$stmt = $user->read();

if ($stmt->rowCount() > 0) {
    $users_list = array();
    $users_list["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $user_item = array(
            "id" => $id,
            "first_name" => $first_name,
            "surname" => $surname,
            "phone" => $phone
        );

        array_push($users_list["records"], $user_item);
    }

    http_response_code(200);

    echo json_encode($users_list);
}
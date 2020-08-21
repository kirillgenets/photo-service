<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/user.php';

function is_matches_search($str) {
    $search_words = explode(' ', $_GET['search']);
    $search_results = array_map(function($item) use ($str) {
        return strpos($str, $item) !== false;
    }, $search_words);

    return in_array(true, $search_results);
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$stmt = $user->read();

if ($stmt->rowCount() > 0) {
    $users_list = array();
    $users_list["content"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if (!is_matches_search($first_name) && !is_matches_search($surname) && !is_matches_search($phone)) {
            continue;
        }

        $user_item = array(
            "id" => $id,
            "first_name" => $first_name,
            "surname" => $surname,
            "phone" => $phone
        );

        array_push($users_list["content"], $user_item);
    }

    http_response_code(200);
    echo json_encode($users_list);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Товары не найдены."), JSON_UNESCAPED_UNICODE);
}
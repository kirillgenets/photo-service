<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

var_dump($_GET);
var_dump($_POST);
var_dump($_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['id']) {
    var_dump($_GET);
} else {
    function is_matches_search($str) {
        if ($_GET['search'] === null) {
            return true;
        }
    
        $search_words = explode(' ', $_GET['search']);
        $search_results = array_map(function($item) use ($str) {
            return strpos($str, $item) !== false;
        }, $search_words);
    
        return in_array(true, $search_results);
    }

    $stmt = $user->read();

    $users_list = array();
    
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
    
        array_push($users_list, $user_item);
    }
    
    http_response_code(200);
    echo json_encode($users_list);
}

?>
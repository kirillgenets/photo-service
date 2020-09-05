<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/user.php';
include_once '../../objects/photo.php';
include_once '../../objects/shared.php';
include_once '../../utils/is_matches_search.php';

session_start();

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$shared = new Shared($db);
$photo = new Photo($db);

if (empty($_SERVER['Authorization']) || $_SERVER['Authorization'] !== $_SESSION['bearer_token']) {
  var_dump($_SERVER['Authorization']);
  var_dump($_SESSION['bearer_token']);
  http_response_code(403);
  echo json_encode(array("error" => "Ошибка доступа."), JSON_UNESCAPED_UNICODE);
} else {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['id']) {
    $data = json_decode(file_get_contents('php://input'));
    $shared->user_id = $_GET['id'];
    $shared->photo_id = $data->photos[0];
  
    $result = $shared->create();
  
    if ($result !== false) {
      $stmt = $photo->read();
      $photos_list = array();
  
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        array_push($photos_list, $id);
      }
  
      http_response_code(201);
      echo json_encode(array("existing_photos" => $photos_list), JSON_UNESCAPED_UNICODE);
    } else {
      http_response_code(503);
      echo json_encode(array("error" => "Невозможно расшарить фотографию (возможно, она уже расшарена)."), JSON_UNESCAPED_UNICODE);
    }
  } else {
    $stmt = $user->read();
  
    $users_list = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
  
      if (!is_matches_search($first_name, $_GET['search']) && !is_matches_search($surname, $_GET['search']) && !is_matches_search($phone, $_GET['search'])) {
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
    echo json_encode($users_list, JSON_UNESCAPED_UNICODE);
  }
}

?>
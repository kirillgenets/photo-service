<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/photo.php';

$database = new Database();
$db = $database->getConnection();

$photo = new Photo($db);
var_dump($_FILES);


function get_validation_errors() {
  $photo_format = $_FILES['photo']['type'];
  $errors = [];
  $is_format_correct =
    substr($photo_format, strlen($photo_format) - 3, 3) == 'png' ||
    substr($photo_format, strlen($photo_format) - 4, 4) == 'jpeg' ||
    substr($photo_format, strlen($photo_format) - 3, 3) == 'jpg';

  if (empty($_FILES['photo'])) {
      $errors['photo'] = 'Файл картинки обязателен!';
  }

  if (!$is_format_correct) {
      $errors['photo'] = "Фотография может быть только в форматах jpg, jpeg и png";
  }

  return $errors;
}

$validation_errors = get_validation_errors($data);

if (count($validation_errors) === 0) {
  $url = '../../storage/' . uniqid() . '_' . $_FILES['photo']['name'];
  move_uploaded_file($_FILES['photo']['tmp_name'], $url);
  // $user->first_name = $data->first_name;
  // $user->surname = $data->surname;
  // $user->phone = $data->phone;
  // $user->password = password_hash($data->password, PASSWORD_DEFAULT);
  // $result = $user->create();

  // if ($result !== false) {
  //     http_response_code(201);
  //     echo json_encode(array("id" => $result), JSON_UNESCAPED_UNICODE);
  // } else {
  //     http_response_code(503);
  //     echo json_encode(array("common" => "Невозможно создать пользователя."), JSON_UNESCAPED_UNICODE);
  // }
} else {
  http_response_code(422);
  echo json_encode($validation_errors, JSON_UNESCAPED_UNICODE);
}

?>
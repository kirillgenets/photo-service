<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

function get_validation_errors($data, $user) {
  $errors = [];

  if (empty($data->phone)) {
      $errors['phone'] = 'Номер телефона обязателен!';
  }

  if (!empty($data->phone) && strlen($data->phone) !== 13) {
      $errors['phone'] = 'Длина номера телефона должна быть 13 символов!';
  }

  if (empty($data->password)) {
      $errors['password'] = 'Пароль обязателен!';
  }

  return $errors;
}

$validation_errors = get_validation_errors($data, $user);

if (count($validation_errors) === 0) {
  $user->phone = $data->phone;
  $user->password = $data->password;

  if ($user->login()) {
    http_response_code(200);
    echo json_encode(array("token" => uniqid('photo-service_')), JSON_UNESCAPED_UNICODE);
  } else {
      http_response_code(404);
      echo json_encode(array("login" => "Incorrect login or password"), JSON_UNESCAPED_UNICODE);
  }
} else {
  http_response_code(422);
  echo json_encode($validation_errors, JSON_UNESCAPED_UNICODE);
}

?>
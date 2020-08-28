<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data, application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/photo.php';

const PAGE_SIZE = 6;

$database = new Database();
$db = $database->getConnection();

$photo = new Photo($db);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_GET['id']) {
  $result = $photo->delete($_GET['id']);

  http_response_code(204);
  echo json_encode(array("success" => "Фотография удалена успешно!!"), JSON_UNESCAPED_UNICODE);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $filename = uniqid() . '_' . $_FILES['photo']['name'] . '.png';

    imagepng(imagecreatefromstring(file_get_contents($_FILES['photo']['tmp_name'])), '../../storage/' . $filename);

    $photo->url = 'http://photo-service/storage/' . $filename;
    $photo->owner_id = $_POST['id'];
    $result = $photo->create();
  
    if ($result !== false) {
      http_response_code(201);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
      http_response_code(503);
      echo json_encode(array("error" => "Невозможно загрузить фотографию."), JSON_UNESCAPED_UNICODE);
    }
  } else {
    http_response_code(422);
    echo json_encode($validation_errors, JSON_UNESCAPED_UNICODE);
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
  $result = $photo->read_one($_GET['id'])->fetch(PDO::FETCH_ASSOC);

  if ($result !== false) {
    http_response_code(200);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  } else {
    http_response_code(404);
    echo json_encode(array("error" => "Нет фотографии с таким идентификатором!"), JSON_UNESCAPED_UNICODE);
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $stmt = $photo->read();

  $photos_list = array();

  $i = 1;
  $max_index = $_GET['page'] * PAGE_SIZE;
  $min_index = $max_index - PAGE_SIZE;

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if ($min_index && $i < $min_index) {
        $i++;
        continue;
      }

      if ($max_index && $i > $max_index) break;

      extract($row);

      $photo_item = array(
          "id" => $id,
          "name" => $name,
          "url" => $url,
          "owner_id" => $owner_id
      );

      array_push($photos_list, $photo_item);

      $i++;
  }

  http_response_code(200);
  echo json_encode($photos_list);
}

?>
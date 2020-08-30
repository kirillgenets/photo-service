<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data, application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/photo.php';

session_start();

const BYTES_IN_MEGABYTE = 1048576;
const PAGE_SIZE = 6;

if (empty($_SERVER['Authorization']) || $_SERVER['Authorization'] !== $_SESSION['bearer_token']) {
  http_response_code(403);
  echo json_encode(array("error" => "Ошибка доступа."), JSON_UNESCAPED_UNICODE);
} else {
  $database = new Database();
  $db = $database->getConnection();

  $photo = new Photo($db);

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && !empty($_GET['id']) && !empty($_GET['owner_id'])) {
    $result = $photo->delete($_GET['id'], $_GET['owner_id']);

    if ($result !== false) {
      http_response_code(204);
      echo json_encode(array("success" => "Фотография удалена успешно!"), JSON_UNESCAPED_UNICODE);
    } else {
      http_response_code(403);
      echo json_encode(array("error" => "Ошибка доступа."), JSON_UNESCAPED_UNICODE);
    }
  } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function are_hashtags_correct() {
      $hashtags_array = explode('#', $_POST['hashtags']);
      $checked_array = array_filter($hashtags_array, function($hashtag) {
        return !strpos($hashtag, ' ');
      });

      return count($checked_array) === count($hashtags_array) && strpos($_POST['hashtags'], '#') === 0;
    }

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

      if (filesize($_FILES['photo']['tmp_name']) > BYTES_IN_MEGABYTE) {
        $errors['photo'] = "Размер фотографии не должен превышать 1 МБ";
      }

      if (!empty($_POST['hashtags']) && !are_hashtags_correct()) {
        $errors['hashtags'] = "Каждый хэштег должен начинаться с символа решетки. Пробелы между хэштегами не ставятся!";
      }
    
      return $errors;
    }
    
    $validation_errors = get_validation_errors($data);
    
    if (count($validation_errors) === 0) {
      $filename = uniqid() . '_' . $_FILES['photo']['name'] . '.png';

      imagepng(imagecreatefromstring(file_get_contents($_FILES['photo']['tmp_name'])), '../../storage/' . $filename);

      $photo->url = 'http://photo-service/storage/' . $filename;
      $photo->owner_id = $_POST['id'];
      $photo->hashtags = $_POST['hashtags'];
      $photo->name = $_POST['name'];
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

        if (!empty($_GET['owner_id']) && $_GET['owner_id'] !== $owner_id) continue;

        $photo_item = array(
            "id" => $id,
            "name" => $name,
            "url" => $url,
            "owner_id" => $owner_id,
            "hashtags" => $hashtags
        );

        array_push($photos_list, $photo_item);

        $i++;
    }

    http_response_code(200);
    echo json_encode($photos_list);
  }
}

?>
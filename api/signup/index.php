<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/user.php';

const SPECIAL_SYMBOLS = ['!', '_', '\-', '#'];

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

function get_validation_errors($data, $user) {
    $errors = [];

    if (empty($data->first_name)) {
        $errors['first-name'] = 'Имя обязательно!';
    }

    if (empty($data->surname)) {
        $errors['surname'] = 'Фамилия обязательна!';
    }

    if (!empty($data->first_name) && preg_match("/[a-z]/", strtolower($data->first_name)) !== 0) {
        $errors['first-name'] = 'Имя может содержать только кириллицу!';
    }

    if (!empty($data->surname) && preg_match("/[a-z]/", strtolower($data->surname)) !== 0) {
        $errors['surname'] = 'Фамилия может содержать только кириллицу!';
    }

    if (empty($data->phone)) {
        $errors['phone'] = 'Номер телефона обязателен!';
    }

    if (!empty($data->phone) && strlen($data->phone) !== 13) {
        $errors['phone'] = 'Длина номера телефона должна быть 13 символов!';
    }

    if (empty($data->password)) {
        $errors['password'] = 'Пароль обязателен!';
    }

    if ($user->exists($data->phone)) {
        $errors['phone'] = "Такой номер телефона уже зарегистрирован!";
    }

    if (!empty($data->password)) {
        if (strlen($data->password) < 6) {
            $errors['password'] = "Пароль должен быть не короче 6 символов!";
        }

        if (strtolower($data->password) === $data->password || strtoupper($data->password) === $data->password) {
            $errors['password'] .= "Пароль должен содержать буквы верхнего и нижнего регистра!";
        }

        if (preg_match_all( "/[0-9]/", $data->password) === 0) {
            $errors['password'] .= " Пароль должен содержать цифру!";
        }

        if (preg_match_all( "/[" . implode("", SPECIAL_SYMBOLS) . "]/", $data->password) === 0) {
            $errors['password'] .= " Пароль должен содержать спецсимвол (один из «!, _, -, #»)!";
        }
    }

    return $errors;
}

$validation_errors = get_validation_errors($data, $user);

if (count($validation_errors) === 0) {
    $user->first_name = $data->first_name;
    $user->surname = $data->surname;
    $user->phone = $data->phone;
    $user->password = password_hash($data->password, PASSWORD_DEFAULT);
    $result = $user->create();

    if ($result !== false) {
        http_response_code(201);
        echo json_encode(array("id" => $result), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(503);
        echo json_encode(array("error" => "Невозможно создать пользователя."), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(422);
    echo json_encode($validation_errors, JSON_UNESCAPED_UNICODE);
}

?>
<?php

include_once('./API/config/database.php');

$db = new Database();

var_dump($db->getConnection());
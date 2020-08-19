<?php

include_once('./config/database.php');

$db = new Database();

var_dump($db->getConnection());
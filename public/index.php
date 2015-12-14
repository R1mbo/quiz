<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../models/Loader.php';
\quiz\models\Loader::registerNamespace('quiz\models', 'd:\xampp\htdocs\quiz\models\\');
\quiz\models\Loader::registerAutoLoad();
echo "start";

$db=\quiz\models\Database::getInstance();

//var_dump($db);

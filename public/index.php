<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../models/Loader.php';

$db = Database::getinstance();

$db->get('questions', array());
$result = $db->result();

$question = $result->question;
$id = $result->id;
include 'html/quiz.php';

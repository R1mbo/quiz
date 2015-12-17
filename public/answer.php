<?php
require_once '../models/Loader.php';
$db = Database::getinstance();

$answer = $_POST['answer'];
$count = $_POST['count'];

Registry::setData('count', $count);

$db->get('answers',array('answer','=',"{$answer}"));
$results = $db->result();

Registry::setData('user_flag', $result->flag);
Registry::setData('question_id', $result->id);
Registry::setData('usr_answer', $answer);

header('location:index.php');
exit;

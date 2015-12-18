<?php
require_once '../models/Loader.php';
$db = Database::getinstance();

$answer = $_POST['answer'];
$count = $_POST['count'];
$question_id = $_POST['question_id'];

$db->get_multiple('*', 'answers', array('answer','=',"{$answer}", 'question_id', '=', $question_id));
$result = $db->result();

$_SESSION['count'] = $count;
$_SESSION['user_flag'] = $result->flag;
$_SESSION['question_id'] = $result->question_id;
$_SESSION['usr_answer'] = $answer;

header('location:index.php');
exit;

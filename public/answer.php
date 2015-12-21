<?php
require_once '../models/Loader.php';
$db = Database::getinstance();

$answer        = $_POST['answer'];
$count         = $_POST['count'];
$is_answered   = $_POST['is_answered'];
$question_id   = $_POST['question_id'];
$session_id    = session_id();

//Update db with answer given by the user and also update question flags
$db->update('sessions', array('question_id'=> $question_id, 'is_answered'=>$is_answered), array('session_id'=>$session_id));

//Select the answer given by the user from the database.
//Provide the answer and the answer status flag to the the session
//Update counter to indicate a question has been answered.
$db->get_multiple('*', 'answers', array('answer','=',"{$answer}", 'question_id', '=', $question_id));

$result = $db->result();

$_SESSION['count']      = $count;
$_SESSION['usr_flag']   = $result->flag;
$_SESSION['usr_answer'] = $answer;

header('location:index.php');
exit;

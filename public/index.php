<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/Loader.php';

function get_question($id='', $count=0){
	$count++;

	$db = Database::getinstance();

	$numbers = range(1,5);
	shuffle($numbers);

	if($id == ''){
		$id = array_slice($numbers, 4);
	}

	$db->get('questions', array('id','=',$id[0]));
	$result = $db->result(); 

	$question = $result->question;
	$question_id = $result->id;

	$result_set = array('count'=>$count, 'question'=>$question, 'id'=>$question_id);

	return $result_set;
}

function get_answers($question_id){

	$db = Database::getinstance();

	$db->get('answers', array('question_id', '=', $question_id));
	$answers = $db->results();

	return $answers;
}

function quiz($id = ''){
	//Set default variable values
	$class = '';
	$btn_state = 'disabled';

	$session_keys = array('usr_answer', 'user_flag', 'question_id');
	foreach($session_keys as $key){
		if(!isset($_SESSION[$key])){
			$usr_answer = '';
			$usr_flag = '';		
		}else {

			$usr_answer = $_SESSION['usr_answer'];
			$usr_flag = $_SESSION['user_flag'];
		}
	}
	if(!isset($count)) $count ='';
	//--------------------------
	
	//Render HTML. Question and Answer
	$question = get_question($id, $count); 
	$html = '<div class="question">'. $question["question"] .'</div>';

	$answers = get_answers($question['id']);
	foreach ($answers as $answer) {
		//If the question id is not empty it means the id
		//is being provided from the session. This means
		//the user has answered a question and the quiz
		//is fetching the same question from the database
		//and then appends a color class to indicate if the
		//given answer was correct or wrong.
		if(!empty($id) && $usr_answer === $answer->answer){
			if($usr_flag == 0) $class = 'wrong';
			if($usr_flag == 1) $class = 'correct';

		}
		//Mark the correct answer with the correct css class
		if(!empty($id) && $answer->flag == 1) $class = 'correct';

		$html .="<form action='answer.php' method='post'>
			<button type='submit' class='answer ".$class."' name='answer' value='$answer->answer'>$answer->answer</button>
			<input type='hidden' name='count' value=".$question['count']." />
			</form>";
		//reset class to nil
		//avoids appending a class to the next
		//iteration of the loop.
		$class = '';

	}
	//If id is not empty user has given an answer
	//Set the btn state to enabled. User can requese
	//a new question.
	//Unset the qeustion id to genereate a new random
	//question
	if(!empty($id)){
		unset( $_SESSION['question_id']);
		$btn_state = 'enabled';
	}
	
	$html .="<div><form action='index.php'><button class='".$btn_state." answer next' ".$btn_state.">Next</button></form></div>";
	return $html;
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="html/css/style.css" type="text/css" rel="stylesheet"/>

</head>
<body>

<div class="container">
<?php 
//var_dump($_SESSION);
if(!isset($_SESSION['question_id'])){
$question_id = '';
}else{
$question_id = $_SESSION['question_id'];
}
if(!$question_id == NULL) echo $quiz = quiz($question_id);	
if($question_id == NULL) echo $quiz = quiz();	
?>

</div>
</body>
</html>

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
	$class = '';
	$usr_answer = Registry::getData('answer');

	$question = get_question($id, $count); 
	$html = '<div class="question">'. $question["question"] .'</div>';
	
	$answers = get_answers($question['id']);
	foreach ($answers as $answer) {
		if(!empty($id) && $usr_answer == $answer->answer){
			switch ($answer->flag) {
				case 0 : {
					$class = 'wrong';
				}
				case 1 : {
					$class = 'correct';
				}
			}
		}
		$html .="<form action='answer.php' method='post'>
			<button type='submit' class='answer ".$class."' name='answer' value='$answer->answer'>$answer->answer</button>
			<input type='hidden' name='count' value=".$question['count']." />
			</form>";

	}
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

$qeustion_id = Registry::getData('question_id');
var_dump($question_id);
if(!$question_id == NULL) echo $quiz = quiz($question_id);	
if($question_id == NULL) echo $quiz = quiz();	
?>

<div><button class='answer next'>Next</button></div>
<?php echo $question['count'];?>
</div>
</body>
</html>

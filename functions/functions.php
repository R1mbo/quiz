<?php

function get_question($id, $count=0){
	//Counts how many times the function
	//was called.
	$count++;

	$db = Database::getinstance();

	$db->get('questions', array('id','=',$id));
	$result = $db->result(); 

	$question = $result->question;
	$question_id = $result->id;

	$result_set = array('count'=>$count, 'question'=>$question, 'id'=>$question_id);

	return $result_set;
}

function get_answers($question_id){

	$db = Database::getinstance();

	$db->get('answers', array('question_id', '=', $question_id));
	$answers = $db->result();

	return $answers;
}

function validate_question($session_id){
	$db = Database::getinstance();
	$db->get('sessions',  array('session_id', '=', $session_id));
	$results = $db->result();
	$count = count($results);

	if($count == 1) {
		$question_id = $results->question_id;
		$is_answered = $results->is_answered;
	}elseif($count == 0){
		$question_id = mt_rand(1, 5);
		$db->insert('sessions', array('session_id'=> $session_id, 'question_id'=> $question_id, 'is_answered'=>0));
		$result_set = array($question_id, 0, $count);
		return $result_set;	
	}
	
	$result_set = array($question_id, $is_answered, $count);
	
	return $result_set;
}

function quiz(){
	$db = Database::getinstance();

	//The counter is used to count how many questions
	//the user has answered. If the var $count is not
	//set the user has not answered any questions.
	if(!isset($count)) $count = 0;
	if(isset($_SESSION['count'])) $count = $_SESSION['count'];

	echo $count;
	//Set default variable values. Variables used for html and
	//quiz logic

	$allowed_questions = 3;
	$class = '';
	$btn_state = 'disabled';
	$answer_state ='';
	$session_id = session_id();
	!isset($_SESSION['next_question']) ? $next_question = 0 : $next_question = $_SESSION['next_question'];
	
        //If the flag next question has the value 0 run validation
	//on the question id to see if there is a question stored
	//against the user session.
	if($next_question == 0){	
		$validate = validate_question($session_id);
		$question_id = $validate[0];
 		$is_answered = $validate[1];
        	$exists      = $validate[2];
	}
	//User has requested a new question. Generate a random
	//question and store it against the user session
	if($next_question == 1){
		$question_id = mt_rand(1,5);	
		$db->update('sessions', array('question_id'=> $question_id, 'is_answered'=>0), array('session_id'=>$session_id));
		unset($_SESSION['next_question']);
	}
	//Used to identify which answer the user has given to a question and if it was the correct
	//one or not.
	!isset($_SESSION['usr_answer']) ? $usr_answer = '' : $usr_answer = $_SESSION['usr_answer'];
	!isset($_SESSION['usr_flag'])   ? $usr_flag = ''   : $usr_flag = $_SESSION['usr_flag'];
	if(!isset($is_answered)) $is_answered = 0;	
	
	//Quiz starts generating questions and related answers here
	$question = get_question($question_id, $count); 

	//Render HTML. Question and Answer
	$html  = '<div class="container">';
	$html .= '<div class="question">'. $question["question"] .'</div>';

	$answers = get_answers($question_id);
	foreach ($answers as $answer) {
		//If the value of is_answered is 1 it means the 
		//question has been answered and the user  and the quiz
		//is fetching the same question from the database.
		//It then appends a color class to indicate if the
		//given answer was correct or wrong.
		if($is_answered == 1 && $usr_answer === $answer->answer){
			if($usr_flag == 0) $class = 'wrong';
			if($usr_flag == 1) $class = 'correct';

		}
		//Mark the correct answer with the correct css class
		//Disable answer buttons if the question has been answered.
		if($is_answered == 1 && $answer->flag == 1) $class = 'correct';
		if($is_answered == 1) $answer_state = 'disabled';

		$html .="<form action='answer.php' method='post'>
			<button type='submit' class='btn neutral ".$class."' name='answer' value='$answer->answer' $answer_state>$answer->answer</button>
			<input type='hidden' name='count' value=".$question['count']." />
			<input type='hidden' name='is_answered' value='1' />
			<input type='hidden' name='question_id' value=".$question_id." />
			</form>";
		//reset class to nil
		//avoids appending a class to the next
		//iteration of the loop.
		$class = '';
	}
	//Enable the button next question
	if($is_answered == 1 && ($count < $allowed_questions)){
		$btn_state = 'enabled';
		$next_question = 1;
	}
	//Check to determine if the quiz has been completed
	//or not. If not display button "next question".
	//If completed display button results.
	if($allowed_questions > $count){	
		$html .="<div><form action='next_question.php' method='post'><button class='btn ".$btn_state."' name='next_question' value=".$next_question." {$btn_state}>Next</button></form></div>";
	}
	elseif($allowed_questions <= $count){
		$html .="<div><form action='result.php'><button class='btn enabled'>View results</button></form></div>";
	}
	$html .= "</div>";
	echo $html;
}
?>

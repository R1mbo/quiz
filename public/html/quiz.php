<!DOCTYPE html>
<html>
<head>
<link href="html/css/style.css" type="text/css" rel="stylesheet"/>

</head>
<body>
<?php $count = Registry::getData('count'); ?>
<?php for($i=0; $i<3; $i++) {?>
<div class="container">
	<?php $question = get_question($id ='',$count);?> 
	<div class="question"><?php echo $question[1] ?></div>

	<?php 
		$answers = get_answers($question[2]);
		foreach ($answers as $answer) {
		echo "<a href='answer.php' class='answer' name='answer'>$answer->answer</a>";
		} 
	?>
	<?php if($next == 0) break; Registry::setData('count', $question[0]); ?>
<?php } ?>	
	<div><button class='answer next'>Next</button></div>
<?php echo Registry::getData('count'); ?>
</div>
</body>
</html>

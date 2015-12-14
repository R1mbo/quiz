<?php
$db->get('answers', array('question_id', '=', $id));
$answers = $db->results();
$answer1 = $db->result();
 //echo '<pre>';
 //var_dump($answer1);
 //echo '</pre>';
?>  
<!DOCTYPE html>
  <html>
  <body>
  <div class="container">
    <div class="question"><?php echo $question ?></div>
 
 <?php foreach ($answers as $answer) {
      echo "<div class='answer'>$answer->answer</div>";
    } 
  ?>
  </div>
  </body>
  </html>

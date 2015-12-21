<?php
require_once '../models/Loader.php';

$_SESSION['next_question'] = $_POST['next_question'];
header('location:index.php');
exit;

<?php
session_start(); if(!isset($_SESSION['student_id'])){header('Location: login.php');exit;} require 'db.php';
$title=trim($_POST['title']??'');$description=trim($_POST['description']??'');
if($title!==''){$q=$db->prepare('INSERT INTO achievements(student_id,title,description) VALUES(?,?,?)');$q->execute([$_SESSION['student_id'],$title,$description]);}
header('Location: dashboard.php');exit;
?>
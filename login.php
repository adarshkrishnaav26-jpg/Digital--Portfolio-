<?php
session_start(); require 'db.php'; $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $q=$db->prepare('SELECT id,name,password FROM students WHERE email=?');$q->execute([trim($_POST['email']??'')]);$u=$q->fetch(PDO::FETCH_ASSOC);
 if($u && password_verify($_POST['password']??'',$u['password'])){session_regenerate_id(true);$_SESSION['student_id']=$u['id'];$_SESSION['student_name']=$u['name'];header('Location: dashboard.php');exit;}
 $msg='Incorrect email or password.';
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title><link rel="stylesheet" href="style.css"></head><body><div class="formbox"><a href="index.php">← Home</a><h1>Student Login</h1><p class="message"><?=htmlspecialchars($msg)?></p><form method="post"><label>Email</label><input type="email" name="email" required><label>Password</label><input type="password" name="password" required><button class="btn">Login</button></form><p>New student? <a href="register.php">Create account</a></p></div></body></html>
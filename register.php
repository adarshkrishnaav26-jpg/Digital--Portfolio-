<?php
session_start(); require 'db.php'; $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $pw=$_POST['password']??'';
 if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($pw)<8){$msg='Enter a name, valid email and password of at least 8 characters.';}
 else {try{$q=$db->prepare('INSERT INTO students(name,email,password) VALUES(?,?,?)');$q->execute([$name,$email,password_hash($pw,PASSWORD_DEFAULT)]);$msg='Account created. You can now log in.';}catch(PDOException $e){$msg='That email may already be registered.';}}
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register</title><link rel="stylesheet" href="style.css"></head><body><div class="formbox"><a href="index.php">← Home</a><h1>Create account</h1><p class="message"><?=htmlspecialchars($msg)?></p><form method="post"><label>Full name</label><input name="name" required maxlength="100"><label>Email</label><input type="email" name="email" required><label>Password (8 characters minimum)</label><input type="password" name="password" minlength="8" required><button class="btn">Register</button></form><p>Already registered? <a href="login.php">Login</a></p></div></body></html>
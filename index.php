<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digital Portfolio</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>My Portfolio</h2>
    <nav>
        <a href="index.php">Home</a>
        <a href="#about">About</a>
        <a href="#skills">Skills</a>
        <a href="#projects">Projects</a>
        <a href="#contact">Contact</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<section class="hero">
    <h1>Hello, Welcome to My Portfolio</h1>
    <h2>I'm a Computer Science Student</h2>
    <p>Exploring Web Development, Python and Artificial Intelligence.</p>
    <a href="#projects" class="btn">View My Projects</a>
</section>

<section id="about">
    <h2>About Me</h2>
    <p>I am a Computer Science student interested in technology,
    programming and software development.</p>
</section>

<section id="skills">
    <h2>My Skills</h2>
    <div class="cards">
        <div>HTML</div>
        <div>CSS</div>
        <div>PHP</div>
        <div>Python</div>
        <div>SQL</div>
    </div>
</section>

<section id="projects">
    <h2>My Projects</h2>
    <div class="cards">
        <div>Digital Portfolio</div>
        <div>Student Management System</div>
        <div>AI Chatbot</div>
    </div>
</section>

<section id="contact">
    <h2>Contact Me</h2>
    <p>Email: your-email@example.com</p>
</section>

<footer>
    <p>© 2026 My Digital Portfolio</p>
</footer>

</body>
</html>

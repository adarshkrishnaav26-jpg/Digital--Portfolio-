<?php
$db = new PDO('sqlite:' . __DIR__ . '/portfolio.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS students (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 name TEXT NOT NULL,
 email TEXT NOT NULL UNIQUE,
 password TEXT NOT NULL,
 created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS achievements (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 student_id INTEGER NOT NULL,
 title TEXT NOT NULL,
 description TEXT,
 status TEXT NOT NULL DEFAULT 'Pending',
 created_at TEXT DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(student_id) REFERENCES students(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS credentials (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 student_id INTEGER NOT NULL,
 title TEXT NOT NULL,
 issuer TEXT NOT NULL,
 issue_date TEXT,
 credential_code TEXT NOT NULL UNIQUE,
 status TEXT NOT NULL DEFAULT 'Valid',
 created_at TEXT DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(student_id) REFERENCES students(id)
)");

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_api";

$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
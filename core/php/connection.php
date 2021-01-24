<?php

// Application Database Connection
$host = [
    "survey" => $_ENV['DB_HOST']
];
$username = [
    "survey" => $_ENV['DB_USER']
];
$password = [
    "survey" => $_ENV['DB_PASS']
];
$database = [
    "survey" => $_ENV['DB_NAME']
];
$port = [
    "survey" => $_ENV['DB_PORT']
];

// System Configuration Connection 
$connection = new mysqli($host['survey'], $username['survey'], $password['survey'], $database['survey']);
if ($connection->connect_error) {
  die("Error connecting to MySQL Server: " . $connection->connect_error);
}


<?php

// Application Database Connection
$host = [
    "survey" => 'localhost'
];
$username = [
    "survey" => 'root'
];
$password = [
    "survey" => ''
];
$database = [
    "survey" => 'ollh_esurvey_live'
];
$port = [
    "survey" => '3306'
];

// System Configuration Connection 
$connection = new mysqli($host['survey'], $username['survey'], $password['survey'], $database['survey']);
if ($connection->connect_error) {
  die("Error connecting to MySQL Server: " . $connection->connect_error);
}

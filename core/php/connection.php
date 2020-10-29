<?php

// Application Database Connection
$host = [
    "survey" => '103.3.60.121'
];
$username = [
    "survey" => 'its-ollh'
];
$password = [
    "survey" => 'OLLH@Manil@70'
];
$database = [
    "survey" => 'ollh_esurvey'
];
$port = [
    "survey" => '3306'
];

// System Configuration Connection 
$connection = new mysqli($host['survey'], $username['survey'], $password['survey'], $database['survey']);
if ($connection->connect_error) {
  die("Error connecting to MySQL Server: " . $connection->connect_error);
}


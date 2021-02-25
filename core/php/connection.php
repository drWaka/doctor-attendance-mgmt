<?php

// Application Database Connection
$host = [
    "dam" => $_ENV['DB_HOST'],
    "bio" => $_ENV['BIO_DB_HOST']
];
$username = [
    "dam" => $_ENV['DB_USER'],
    "bio" => $_ENV['BIO_DB_USER']
];
$password = [
    "dam" => $_ENV['DB_PASS'],
    "bio" => $_ENV['BIO_DB_PASS']
];
$database = [
    "dam" => $_ENV['DB_NAME'],
    "bio" => $_ENV['BIO_DB_NAME']
];
$port = [
    "dam" => $_ENV['DB_PORT'],
    "bio" => ''
];

// System Configuration Connection 
$connection = new mysqli($host['dam'], $username['dam'], $password['dam'], $database['dam'], $port['dam']);
if ($connection->connect_error) {
  die("Error connecting to MySQL Server: " . $connection->connect_error);
}

// Biometrics Database Connection
$bioConnection = '';
try {
    $bioConnection = new PDO(
    "sqlsrv:server={$host['bio']};Database={$database['bio']}",
    $username['bio'],
    $password['bio'],
    array(
      //PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    )
  );
} catch (PDOException $error) {
  die("Error connecting to SQL Server: " . $error -> getMessage());
}
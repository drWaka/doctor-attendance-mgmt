<?php

session_start();

unset($_SESSION['userId']);
unset($_SESSION['firstName']);
unset($_SESSION['lastName']);

header("Location: ../../admin/login.php");

<?php
    $isSessionValid = 1;
    $basename = basename($_SERVER['PHP_SELF'], '.php');

    if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
        $userRecord = UserMstr::show($_SESSION['userId']);
        if (!(count($userRecord) > 0)) {
            unset($_SESSION['userId']);
            $isSessionValid = 0;
        }
    } else {
        $isSessionValid = 0;
    }

    if ($isSessionValid == 1) {
        if ($basename == 'login') {
            header('Location: homepage.php');
        }
    } else {
        if ($basename !== 'login') {
            header('Location: login.php');
        }
    }
?>
<?php
    session_start();
    if (isset($_SESSION["login_session"])) {
        unset($_SESSION["login_session"]);
    }
    header("Location: index.php");
    exit();
?>
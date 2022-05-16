<?php
    session_start();
    $_SESSION['matricule'] = '';
    $_SESSION['identifiant'] = '';
    $_SESSION = [];
    session_destroy();
    header('location:../index.php');
    exit();
?>
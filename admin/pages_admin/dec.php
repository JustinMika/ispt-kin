<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    require_once '../../includes/log_user.class.php';
    LogUser::addlog(VerificationUser::verif($_SESSION['id_user']['noms']), "s'est déconnecté(e) dans le system.");
    $_SESSION['data'] = array();
    session_destroy();
    header('location:../');
    exit();
?>
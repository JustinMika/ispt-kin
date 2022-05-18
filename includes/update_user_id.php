<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour de la faculte
    // print_r($_POST);

    if(isset($_POST['id']) && !empty($_POST['id']) &&
    isset($_POST['username']) && !empty($_POST['username']) &&
    isset($_POST['email']) && !empty($_POST['email'])){
        // mise a jour
        $sql = "UPDATE utilisateurs SET noms = ?, email = ? WHERE id_user = ?";
        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute(array($_POST['username'], $_POST['email'], $_POST['id']));
        if($ok){
            echo 'ok';
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a mit a jour les identifiants');
        }else{
            echo 'Echec de mise a jour, contactez le developpeur svp...';
        }
    }
?>
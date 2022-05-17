<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour de la faculte

    if(isset($_POST['id_fac_a_modifier']) && !empty($_POST['id_fac_a_modifier']) &&
    isset($_POST['fac_a_modifier']) && !empty($_POST['fac_a_modifier'])){
        // mise a jour
        $sql = "UPDATE faculte SET fac = ? WHERE id  = ?";
        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute(array($_POST['fac_a_modifier'], $_POST['id_fac_a_modifier']));
        if($ok){
            echo 'ok';
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a mit a jour une faculté');
        }else{
            echo 'Echec de mise a jour de la faculté, ...';
        }
    }
?>
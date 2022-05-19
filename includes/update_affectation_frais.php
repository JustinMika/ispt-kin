<?php 
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';
    
    if(isset($_POST['_id_type_frais_']) && !empty($_POST['montant_'])){
        // update prevision
        $prevision = ConnexionBdd::Connecter()->prepare("UPDATE prevision_frais SET montant = ? WHERE id_frais = ?");
        $ok = $prevision ->execute(array($_POST['montant_'], $_POST['_id_type_frais_']));

        if($ok){
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a mis à jour le montant de prévision');
            die("ok");
        }else{
            die("Mise a jour echouer");
        }
    }
?>
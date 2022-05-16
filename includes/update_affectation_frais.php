<?php 
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['type_frais_']) && !empty($_POST['type_frais_'])){
        // update prevision
        $prevision = ConnexionBdd::Connecter()->prepare("UPDATE prevision_frais SET montant = ? WHERE type_frais = ? AND annee_acad = ? AND faculte = ? AND promotion = ?");
        $ok = $prevision ->execute(array($_POST['montant_'], $_POST['type_frais_'], $_POST['annee_acad'], $_POST['faculte'], $_POST['promotion']));

        if($ok){
            // update affectation
            $prevision = ConnexionBdd::Connecter()->prepare("UPDATE affectation_frais SET montant = ? WHERE type_frais = ? AND annee_acad = ? AND faculte = ? AND promotion = ?");
            $ok = $prevision ->execute(array($_POST['montant_'], $_POST['type_frais_'], $_POST['annee_acad'], $_POST['faculte'], $_POST['promotion']));
            if($ok){
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a mis à jour le montant de prévision pour : le '.$_POST['type_frais_'].' en '.$_POST['promotion'].'-'.$_POST['faculte'].' cette année');
                die("ok");
            }else{
                die("Mise a jour echouer");
            }
        }else{
            die("Mise a jour echouer");
        }
    }
?>
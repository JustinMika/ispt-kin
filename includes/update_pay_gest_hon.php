<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['id']) && !empty($_POST['id'])){
        if(isset($_POST['montant']) && !empty($_POST['montant'])){
            // mise a jour
            // print_r($_POST);
            $update = ConnexionBdd::Connecter()->prepare("UPDATE gest_honoraire SET total_payer = ? WHERE id = ?");
            $ok = $update->execute(array($_POST['montant'], $_POST['id']));
            if($ok){
                echo 'ok';
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "a il(elle) éffectué(e) sur le honohaire des enseignants");
            }else{
                echo 'mise a jour du montant echouer';
            }
        }else{
            echo 'Veuillez saisir un montant valide svp ...';
        }
    }else{
        echo 'Veuillez completer tous les elements relative au formulaire svp ...';
    }
?>
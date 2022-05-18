<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    if(isset($_POST['id_id_id']) && !empty($_POST['id_id_id'])){
        if(isset($_POST['montant_ru']) && !empty($_POST['montant_ru'])){
            $update = ConnexionBdd::Connecter()->prepare("UPDATE previson_frais_univ SET montant = ? WHERE id = ?");
            $ok = $update->execute(array($_POST['montant_ru'], $_POST['id_id_id']));
            if($ok){ 
                echo('ok');
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a mit a jour le montant pour une prevision de frais universitaire...');
            } else {
                echo 'les donnees ne sont pas mis a jour';
            }
        } else {
            echo 'le montant est vide veuillez le renseigner svp ...';
        }
    }else{
        echo 'Completer tous les champs svp...';
    }
?>
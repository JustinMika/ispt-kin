<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    
    if(isset($_POST['id_poste_rec_univ']) && !empty($_POST['id_poste_rec_univ'])){
        $d = ConnexionBdd::Connecter()->prepare("DELETE FROM previson_frais_univ WHERE id  = ?");
        $ok = $d->execute(array($_POST['id_poste_rec_univ']));

        if($ok){
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a supprimé(e) une prévision academique.');
            echo 'ok';
        }else{
            echo "une erreur est survenue lors de la suppression...";
        }
    }else if(isset($_POST['id_poste_dep']) && !empty($_POST['id_poste_dep'])){
        // print_r($_POST);
        $d = ConnexionBdd::Connecter()->prepare("DELETE FROM depense_facultaire WHERE id_pdf  = ?");
        $ok = $d->execute(array($_POST['id_poste_dep']));
        if($ok){
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a supprimé(e) un poste de dépense facultaire.');
            echo 'ok';
        }else{
            echo "une erreur est survenue lors de la suppression...";
        }
    }else if(isset($_POST['id_etud_delete']) && !empty($_POST['id_etud_delete'])){
        $d = ConnexionBdd::Connecter()->prepare("DELETE FROM etudiants_inscrits WHERE id = ?");
        $ok = $d->execute(array($_POST['id_etud_delete']));

        if($ok){
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a supprimé(e) un(e) étudiant(e).');
            echo 'ok';
        }else{
            echo "une erreur est survenue lors de la suppression...";
        }
    } 
?>
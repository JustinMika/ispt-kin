<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour de la faculte

    if(isset($_POST['id_m_annee_acad']) && !empty($_POST['id_m_annee_acad']) &&
    isset($_POST['update_ann_acad']) && !empty($_POST['update_ann_acad'])){
        // mise a jour
        $sql = "UPDATE annee_academique SET annee_acad = ? WHERE id  = ?";
        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute(array(htmlentities(htmlspecialchars(trim($_POST['update_ann_acad']))), htmlentities(htmlspecialchars(trim($_POST['id_m_annee_acad'])))));
        if($ok){
            echo 'ok';
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a mit à jour une anneée academique.');
        }else{
            echo 'Echec de mise à jour de l anneée academique, ...';
        }
    }
?>
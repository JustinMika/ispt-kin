<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour de la faculte

    if(isset($_POST['id_m_annee_acad']) && !empty($_POST['id_m_annee_acad']) &&
    isset($_POST['update_ann_acad']) && !empty($_POST['update_ann_acad'])){
        // mise a jour
        $sql = "UPDATE annee_acad SET annee_acad = ? WHERE id_annee  = ?";
        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute(array(htmlspecialchars(trim($_POST['update_ann_acad'])), 
        $_POST['id_m_annee_acad']));
        if($ok){
            echo 'ok';
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a mit à jour une anneée academique.');
        }else{
            echo 'Echec de mise à jour de l anneée academique, ...';
        }
    }
?>
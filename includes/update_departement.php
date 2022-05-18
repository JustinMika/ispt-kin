<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour du departement
    // print_r($_POST);

    if(isset($_POST['id_departement']) && !empty($_POST['id_departement']) &&
    isset($_POST['departement']) && !empty($_POST['departement'])){
        // mise a jour
        $sql = "UPDATE departement SET departement = ? WHERE id_departement  = ?";
        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute(array($_POST['departement'], $_POST['id_departement']));
        if($ok){
            echo 'ok';
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a mit a jour du departement,...');
        }else{
            echo 'Echec de mise a jour du departement, ...';
        }
    }else{
        die("Veuillez completez tous les champs.");
    }
?>
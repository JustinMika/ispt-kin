<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';
    
    $date_p = $_POST['date_p_update'];
    if($date_p <= date("Y-m-d")){
        $mat  = $_POST['mat_update'];
        $pay_etud_p = $_POST['montant_update'];

        $sql = "UPDATE `payement` SET `montant` = ?, `date_payement` = ? WHERE `payement`.`id_payement` = ?";

        $sql_array = array($_POST['montant_update'], $date_p, $_POST['id_payement_etud']);

        $update = ConnexionBdd::Connecter()->prepare($sql);
        $ok = $update->execute($sql_array);

        if($ok){
            echo "ok";
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a modifier le montant payer par '.$mat);
        }else{
            die("Une erreur est survenue veuillez de bien verifier les informations.");
        }
    }else{
        die("La date de payement doit être inferieur ou égale à la date d'aujourdhui");
    }
?>
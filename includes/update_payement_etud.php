<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';
    
    $date_p = $_POST['date_p_update'];
    if($date_p <= date("Y-m-d")){
        $mat  = $_POST['mat_update'];
        $promotion = $_POST['promotion_update'];
        $fac = $_POST['fac_update'];
        $annee_acad = $_POST['annee_acad_update'];
        $type_frais = $_POST['type_frais_update'];
        $pay_etud_p = $_POST['montant_update'];

        $tot_aff = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as tot_af FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?");
        $tot_aff->execute(array($mat, $promotion, $fac, $annee_acad, $type_frais));
        if($tot_aff->rowCount() > 0){
            $v_tot_aff = $tot_aff->fetch();
            $montant_t_p = $v_tot_aff['tot_af'];

            // // le montant
            $verif = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS montant FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND date_payement = ? AND type_frais = ?");
            $verif->execute(array($mat, $fac, $promotion, $annee_acad, date("Y-m-d", strtotime($date_p)), $type_frais));
            $v_verif = $verif->fetch();
            $montant_t_a_payer = $v_verif['montant'];
            if(intval($pay_etud_p) <= intval($montant_t_p)){
                $sql = "UPDATE `payement` SET `montant` = ?, `date_payement` = ? WHERE `payement`.`id` = ?";

                $sql_array = array($_POST['montant_update'], $date_p, $_POST['id_payement_etud']);

                $update = ConnexionBdd::Connecter()->prepare($sql);
                $ok = $update->execute($sql_array);

                if($ok){
                    echo "ok";
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a modifier le montant payer par '.$mat.'-'.$fac.' pour le '.$type_frais);
                }else{
                    die("Une erreur est survenue veuillez de bien verifier les informations.");
                }
            }else{
                echo 'le montant '.strtolower(intval($pay_etud_p)).'$ pour le '.$type_frais.' est superieur a celui affecter a l\'etudiant : <b>['.$mat.'-'.$promotion.'-'.$fac.']</b>';
            }
        }else{
            echo "Veuillez verifier si le type de frais est affecte a l  etudiant";
        }
    }else{
        die("La date de payement doit être inferieur ou égale à la date d'aujourdhui");
    }
?>
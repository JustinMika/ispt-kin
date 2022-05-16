<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    // print_r($_POST);

    if(isset($_POST['mat_etud_payement']) && !empty($_POST['mat_etud_payement']) &&
    isset($_POST['promotion_etud_p']) && !empty($_POST['promotion_etud_p']) &&
    isset($_POST['fac_etud_payemt']) && !empty($_POST['fac_etud_payemt']) &&
    isset($_POST['type_frais_p_etud']) && !empty($_POST['type_frais_p_etud']) &&
    isset($_POST['pay_etud_p']) && !empty($_POST['pay_etud_p']) &&
    isset($_POST['pay_num_bordereau']) && !empty($_POST['pay_num_bordereau']) &&
    isset($_POST['date_p']) && !empty($_POST['date_p']) &&
    isset($_POST['annee_acad_pay_etud']) && !empty($_POST['annee_acad_pay_etud'])){
        $mat = $_POST['mat_etud_payement'];
        $fac = $_POST['fac_etud_payemt'];
        $promotion = $_POST['promotion_etud_p'];
        $annee_acad = $_POST['annee_acad_pay_etud'];
        $type_frais = $_POST['type_frais_p_etud'];
        $pay_etud_p = $_POST['pay_etud_p'];
        $pay_num_bordereau = $_POST['pay_num_bordereau'];
        $date_p = $_POST['date_p'];
        // $date_p = $date_p;
        // selection des frais que l'etudiants doit payer
        $sql_req = "SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion  = ? AND annee_academique = ?";
        $sel_etudiant = ConnexionBdd::Connecter()->prepare($sql_req);
        $sel_etudiant->execute(array($mat, $fac, $promotion,  $annee_acad));

        $n = $sel_etudiant->rowCount();

        if($n > 0){
            // on verifie si les frais qu on veut payer a l' etudiant luii est affecte
            $sql_t_frais = "SELECT * FROM affect_fac_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?";
            $verif_t_frais = ConnexionBdd::Connecter()->prepare($sql_t_frais);
            $verif_t_frais->execute(array($mat, $promotion, $fac,  $annee_acad, $type_frais));

            $n_result = $verif_t_frais->rowCount();

            if($n_result > 0){
                // on verifie si le payement n pas dans la base de donnees. meme si le numero de bordereau est unique pour chaque payement : on est sait jamais : il faut jamais faire confiance aux p*tain des utilisateurs.
                $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement_fac_frais WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND date_payement = ? AND type_frais = ? AND num_borderon = ? AND montant = ?");
                $verif->execute(array($mat, $fac, $promotion, $annee_acad, $date_p, $type_frais, $pay_num_bordereau, $pay_etud_p));
                            
                $nbre = $verif->rowcount();
                $d = strtolower($date_p);
                if($date_p <= date("Y-m-d")){
                    if($nbre <= 0){
                        $tot_aff = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as tot_af FROM affect_fac_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?");
                        $tot_aff->execute(array($mat, $promotion, $fac, $annee_acad, $type_frais));
                        $v_tot_aff = $tot_aff->fetch();
                        $montant_t_p = $v_tot_aff['tot_af'];

                        // // le montant
                        $verif = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS montant FROM payement_fac_frais WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND date_payement = ? AND type_frais = ?");
                        $verif->execute(array($mat, $fac, $promotion, $annee_acad, date("Y-m-d", strtotime($date_p)), $type_frais));
                        $v_verif = $verif->fetch();
                        $montant_t_a_payer = $v_verif['montant'];

                        if(intval($montant_t_a_payer + $pay_etud_p) <= intval($montant_t_p)){
                            // on procede au payement
                            // insertion de donnees dans la base de donnees 
                            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO payement_fac_frais(matricule, faculte, promotion, annee_acad, date_payement, type_frais, num_borderon, montant) VALUES(?,?,?,?,?,?,?,?)");
                            $ok = $insert_etud->execute(array($mat, $fac, $promotion, $annee_acad, date("Y-m-d", strtotime($date_p)), $type_frais, $pay_num_bordereau, $pay_etud_p));
                            if($ok){
                                echo ("Donnees insere");
                                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a effectué(e) un payement de frais facultaire à partir du formulaire');
                            }else{
                                echo ("une erreur est survenue. reesayer plus tard ...");
                            }
                        }else{
                            echo 'le montant '.strtolower(intval($montant_t_a_payer + $pay_etud_p)).'$ pour le '.$type_frais.' est superieur a celui affecter a l\'etudiant : <b>['.$mat.'-'.$promotion.'-'.$fac.']</b>';
                        }
                    }else{
                        die("le payement existe deja dans la base de donnees. Veuillez verifier le bordereau avant ...");
                    }
                }else{
                    die("La date doit être inferieur ou égale à la date d'aujourd hui.");
                }
            }else{
                die("le ".$type_frais." n'est pas affecter a l'etudiant <b>[".$mat."-".$promotion."-".$fac."]</b>");
            }
        }else{
            die("l'etudiant <b>[".$mat."-".$promotion."-".$fac."]</b> n'est pas inscrit");
        }
    }else{
        die("Veuillez completer tous les champs svp...");
    }
?>
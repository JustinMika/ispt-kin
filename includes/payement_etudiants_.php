<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    // print_r($_POST);
    if(!empty($_POST['mat_etudiants_']) && !empty($_POST['annee_acad_pay']) &&
    !empty($_POST['section_etud']) && !empty($_POST['departement_etud']) &&
    !empty($_POST['option_etu']) && !empty($_POST['type_d_frais']) && !empty($_POST['numer_border_']) && !empty($_POST['date_pay_etud'])
    && !empty($_POST['montant_pay_et'])){
        // verification si la date n est pas superieur a celle d 'aujourdhui
        $num_b = strtoupper($_POST['numer_border_']);
        $date_p = $_POST['date_pay_etud'];
        $d = strtolower($_POST['date_pay_etud']);
        if($date_p <= date("Y-m-d")){
            // verification du numero de bordereau
            $rnum = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE num_bordereau = ?");
            $rnum->execute(array($num_b));
            if($rnum->rowCount() <= 0){
                $sql = "SELECT
                            affectation_frais.matricule,
                            affectation_frais.id_section,
                            affectation_frais.id_departement,
                            affectation_frais.id_option,
                            affectation_frais.id_annee,
                            SUM(prevision_frais.montant) as tot_m,
                            prevision_frais.type_frais
                        FROM
                            affectation_frais
                        LEFT JOIN prevision_frais ON affectation_frais.id_frais = prevision_frais.id_frais
                        WHERE
                            affectation_frais.matricule = ? AND affectation_frais.id_section = ? AND affectation_frais.id_departement = ? AND affectation_frais.id_option = ? AND affectation_frais.id_annee = ? AND affectation_frais.id_frais = ?";
                $type_f = ConnexionBdd::Connecter()->prepare($sql);
                $type_f->execute(array($_POST['mat_etudiants_'], 
                                        $_POST['section_etud'], 
                                        $_POST['departement_etud'], 
                                        $_POST['option_etu'], 
                                        $_POST['annee_acad_pay'], 
                                        $_POST['type_d_frais']));
                if($type_f->rowCount() > 0){
                    $data_f = $type_f->fetch();
                    $total_f = $data_f['tot_m'];
                    if($_POST['montant_pay_et'] <= $total_f){
                        $sql = "INSERT INTO payement(montant, date_payement, num_bordereau, matricule, id_frais, id_section, id_departement, id_option, id_annee) VALUES(?,?,?,?,?,?,?,?, ?)";
                        $insertion_frais  = ConnexionBdd::Connecter()->prepare($sql);
                        $ok = $insertion_frais->execute(array($_POST['montant_pay_et'],
                            $date_p, $num_b, 
                            $_POST['mat_etudiants_'], $_POST['type_d_frais'],
                            $_POST['section_etud'], 
                            $_POST['departement_etud'], 
                            $_POST['option_etu'], 
                            $_POST['annee_acad_pay']));
                        if($ok){
                            die("ok");
                        }else{
                            die("Une erreur est survenue, veuillez reesayer.");
                        }
                    }else{
                        die("le montant {$_POST['montant_pay_et']} depasse celui qui est affecter a l'etudiant(e)");
                    }
                }else{
                    die("Le type de frais selectionner n'est pas affetcer à 'étudiant(e)");
                }
            }else{
                die("Le numero {$num_b} existe pour un autre payement.");
            }
        }else{
            echo "La date est superieur a ".date("Y-m-d");
        }
    }else{
        die("Veuillez remplir tous les champs.");
    }
?>
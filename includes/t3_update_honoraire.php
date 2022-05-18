<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // mise a jour de info de l enseignant sur les honoraire
    // print_r($_POST);
    if(!empty($_POST['update']) && $_POST['update'] == "update"){
        if(isset($_POST['noms_enseign_']) && isset($_POST['grade_enseign_'])){
            if(isset($_POST['faculte_gh']) && isset($_POST['cours_enseign_'])){
                if(isset($_POST['type_enseignant']) && isset($_POST['type_prestation'])){
                    if(isset($_POST['taux']) && isset($_POST['heure_t'])){
                        if(isset($_POST['montant_ht']) && isset($_POST['heure_pr'])
                        && isset($_POST['montant_pr']) && isset($_POST['total_gen']) 
                        && isset($_POST['id_update'])){
                            $noms_enseign_ = htmlspecialchars(trim($_POST['noms_enseign_']));
                            $grade_enseign_ = htmlspecialchars(trim($_POST['grade_enseign_']));
                            $faculte_gh = htmlspecialchars(trim($_POST['faculte_gh']));
                            $cours_enseign_ = htmlspecialchars(trim($_POST['cours_enseign_']));
                            $type_enseignant = htmlspecialchars(trim($_POST['type_enseignant']));
                            $type_prestation = htmlspecialchars(trim($_POST['type_prestation']));
                            $taux = htmlspecialchars(trim($_POST['taux']));
                            $heure_t = htmlspecialchars(trim($_POST['heure_t']));
                            $montant_ht = htmlspecialchars(trim($_POST['montant_ht']));
                            $heure_pr = htmlspecialchars(trim($_POST['heure_pr']));
                            $montant_pr = htmlspecialchars(trim($_POST['montant_pr']));
                            $total_gen = htmlspecialchars(trim($_POST['total_gen']));
                            
                            // mise a jour
                            // print_r($_POST);
                            $v = ConnexionBdd::Connecter()->prepare("UPDATE gest_honoraire SET noms_ens = ?, grade_ens = ?, cours = ?, 
                            id_section = ?, heure_th = ?, montant_ht = ?, heure_pr = ?, montant_hp = ?, taux = ?, total = ?, type_enseig = ?, prestation = ? WHERE id = ?");
                            $true  = $v->execute(array($noms_enseign_, $grade_enseign_, $cours_enseign_, $faculte_gh, $heure_t, 
                            $montant_ht, $heure_pr, $montant_pr, $taux, $total_gen, $type_enseignant, $type_prestation, $_POST['id_update']));

                            if($true){
                                echo 'ok';
                                LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "il(elle) a update les informations de l' enseignants '{$noms_enseign_}'.");
                            }else{
                                echo "Une erreur inconue est survenue, les donnees ne sont pas mise a jour";
                            }
                        }else{
                            echo 'Veuillez renseigner le total gen';
                        }
                    }else{
                        echo 'Veuillez renseigner le taux';
                    }
                }else{
                    echo 'Veuillez renseigner le volume horaire';
                }
            }else{
                echo 'Veuillez renseigner le cours';
            }
        }else{
            echo 'Veuillez renseigner les noms de l enseignant';
        }
    }else if(!empty($_POST['delete_gh']) && $_POST['delete_gh'] == "delete_gh"){
        # code...
        if(!empty($_POST['id_delete_gh'])){
            $n = ConnexionBdd::Connecter()->prepare("DELETE FROM gest_honoraire WHERE id = ?");
            $ok = $n->execute(array($_POST['id_delete_gh']));
            if($ok){
                die("ok");
            }else{
                die("Il ya une erreur lors de la suppression.");
            }
        }else{
            die("Certains champs sont vide");
        } //180NQ923402
    }
    //t3 de payements facultaires
    else if(!empty($_POST['del_p']) && $_POST['del_p'] == "del_p"){
        if(!empty($_POST['id_form_del_p'])){
            $del = ConnexionBdd::Connecter()->prepare("DELETE FROM payement_fac_frais WHERE id = ?");
            $ok = $del->execute(array($_POST['id_form_del_p']));
            if($ok){
                die("ok");
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a supprimer un enregistrement de payement d un etudiant(e)');
            }else{
                die("Une erreur est survenue");
            }
        }else{
            die("Veuillez selectioner un element,...");
        }
    }else if(!empty($_POST['update_payemt_etud']) && $_POST['update_payemt_etud'] == "update_payemt_etud"){
        if(!empty($_POST['x_id_pay_etud']) && !empty($_POST['x_mat_pay_etud']) && !empty($_POST['x_fac_pay_etud']) && !empty($_POST['x_pr_pay_etud']) && !empty($_POST['x_date_pay_etud']) && !empty($_POST['x_type_f_pay_etud']) && !empty($_POST['x_num_b_pay_etud']) && !empty($_POST['x_montant_pay_etud'])){
            $sel = ConnexionBdd::Connecter()->prepare("SELECT * FROM WHERE num_borderon = ?");
            $sel->execute(array());

            if($sel->rowCount() <= 0){
                $update = ConnexionBdd::Connecter()->prepare("UPDATE payement_fac_frais SET matricule = ?, faculte= ?, promotion = ?,date_payement= ?, type_frais= ?, num_borderon=?, montant=? WHERE id = ?");
                $ok = $update->execute(array($_POST['x_mat_pay_etud'], $_POST['x_fac_pay_etud'], $_POST['x_pr_pay_etud'], $_POST['x_date_pay_etud'], $_POST['x_type_f_pay_etud'], $_POST['x_num_b_pay_etud'], $_POST['x_montant_pay_etud'], $_POST['x_id_pay_etud']));
                if($ok){
                    die("ok");
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), "a mis a jour le payement de {$_POST['x_mat_pay_etud']} qui est en {$_POST['x_fac_pay_etud']}:{$_POST['x_pr_pay_etud']}");
                }else{
                    die("La mise a jour a echouer, ...");
                }
            }else{
                die("Le numero de borderau : {$_POST['x_num_b_pay_etud']} existe deja pour un autre payement.");
            }
        }else{
            die("Certains champs sont vide,...");
        }
    }else if(!empty($_POST['update_etudiants_pwd']) && $_POST['update_etudiants_pwd'] == "update_etudiants_pwd"){
        if(isset($_POST['mat']) && isset($_POST['pwd'])){
            $pwd = htmlspecialchars(trim(sha1($_POST['pwd'])));
            $u = ConnexionBdd::Connecter()->prepare("UPDATE etudiants SET password = ? WHERE matricule = ?");
            $ok = $u->execute(array($pwd, $_POST['mat']));
            if($ok){
                $u = ConnexionBdd::Connecter()->prepare("UPDATE etudiants_inscrits SET password = ? WHERE matricule = ?");
                $ok = $u->execute(array($pwd, $_POST['mat']));
                if($ok){
                    echo 'ok';
                }else{
                    die("Une erreur s'est produit,...");   
                }
            }else{
                die("Une erreur s'est produit,...");
            }
        }else{
            die("le matricule est vide.");
        }
    }else{
        die("Une Erreur est survenu");
    }
?>
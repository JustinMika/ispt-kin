<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';
    // print_r($_POST);
    if(!empty($_POST['noms_enseign_']) && !empty($_POST['grade_enseign_'])){
        if(!empty($_POST['faculte_gh']) && !empty($_POST['cours_enseign_'])){
            if(!empty($_POST['type_enseignant']) && !empty($_POST['type_prestation'])){
                if(!empty($_POST['taux']) && !empty($_POST['heure_t'])){
                    if(!empty($_POST['montant_ht']) && !empty($_POST['heure_pr'])
                    && !empty($_POST['montant_pr']) && !empty($_POST['total_gen'])){
                        // on recupere le dernier annee academique
                        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
                        $an_r = $an->fetch();

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

                        /**noms_ens	grade_ens	cours	heure_th	montant_th	heure_pr	montant_hp	taux	total	total_payer	type_enseig	prestation	id_annee	id_section */
                        // print_r($_POST);
                        if(!empty($an_r)){
                            $annee_acad = $an_r['id_annee'];
                            
                            $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE noms_ens = ? AND grade_ens = ? AND cours = ? AND id_annee = ? AND id_section = ?");
                            $v->execute(array($noms_enseign_, $grade_enseign_, $cours_enseign_, $annee_acad, $faculte_gh));

                            if($v->rowCount() <= 0){
                                // insertion
                                $v = ConnexionBdd::Connecter()->prepare("INSERT INTO gest_honoraire(noms_ens, grade_ens, cours, heure_th, montant, heure_pr, montant_hp, taux, total, type_enseig, prestation, id_annee, id_section) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
                                $true  = $v->execute(array($noms_enseign_, $grade_enseign_, $cours_enseign_, $heure_t, 
                                $montant_ht, $heure_pr, $montant_pr, $taux, $total_gen, $type_enseignant, $type_prestation, $annee_acad, $faculte_gh));

                                if($true){
                                    echo 'ok';
                                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "il(elle) a ajouter un enseignants.");
                                }else{
                                    echo "Une erreur inconue est survenue, les donnees ne sont enresitre dans la base deonnees,";
                                }
                            }else{
                                echo 'les donnees existe déjà';
                            }
                        }else{
                            echo 'Veuillez enregistrer premierement l annee academique.';
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
?>
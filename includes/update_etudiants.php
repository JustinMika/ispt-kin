<?php
    #updateetudiants.php
    session_start();
    require './ConnexionBdd.class.php';
    require './verification.class.php';
    require './log_user.class.php';

    if(isset($_POST['update_mat']) && !empty($_POST['update_mat']) && isset($_POST['update_annee_acad']) && !empty($_POST['update_annee_acad'])){   
        // update
        if(!empty($_POST['old_fac']) && !empty($_POST['old_promotion'])){
            $old_fac = $_POST['old_fac'];
            $old_promotion = $_POST['old_promotion'];

            if($old_fac == $_POST['update_fac'] && $old_promotion == $_POST['update_promotion']){
                $update = ConnexionBdd::Connecter()->prepare("UPDATE etudiants_inscrits SET noms = ?, fac = ?,promotion = ? WHERE matricule  = ? AND annee_academique = ?");
                $ok = $update->execute(array($_POST['update_noms'],
                    $_POST['update_fac'],
                    $_POST['update_promotion'],
                    $_POST['update_mat'],
                    $_POST['update_annee_acad']));
                if($ok){
                    echo 'ok';
                }else{
                    echo "une Erreur est survenue : veuillez verifier les informations avant d'effectuer une mise a jour ...";
                }
            }else if($old_fac != $_POST['update_fac'] && $old_promotion == $_POST['update_promotion']){
                // on lui update
                $update = ConnexionBdd::Connecter()->prepare("UPDATE etudiants_inscrits SET noms = ?, fac = ?,promotion = ? WHERE matricule  = ? AND annee_academique = ?");
                $ok = $update->execute(array($_POST['update_noms'],
                    $_POST['update_fac'],
                    $_POST['update_promotion'],
                    $_POST['update_mat'],
                    $_POST['update_annee_acad']));
                $affect = "";
                $pay = "";
                // on recupere ses affectations
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM affectation_frais WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $affect .= $d_aff['type_frais'].':'.$d_aff['montant'].', ';
                }

                // on recupere ses payements
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $pay .= $d_aff['type_frais'].':'.$d_aff['montant'].':'.$d_aff['date_payement'].':'.$d_aff['num_borderon'].', ';
                }

                // puis on efface ses informations

                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM affectation_frais WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM payement WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $titre = 'l etudiant : '.$_POST['update_mat'].' qui etait en '.$old_promotion.' '.$old_fac.' ses affectations : '.$affect.' | ses payements '.$pay; 
                // on enregistre dans la corbeille
                $d = ConnexionBdd::Connecter()->prepare("INSERT INTO corbeille(titre, date, noms_del) VALUES(?, NOW(), ?)");
                $ok = $d->execute(array($titre, VerificationUser::verif($_SESSION['data']['noms'])));
                if($ok){
                    echo 'ok';
                }else{
                    echo 'veuillez remplir tous les champs svp ...';
                }
            }else if($old_fac == $_POST['update_fac'] && $old_promotion != $_POST['update_promotion']){
                // on lui update
                $update = ConnexionBdd::Connecter()->prepare("UPDATE etudiants_inscrits SET noms = ?, fac = ?,promotion = ? WHERE matricule  = ? AND annee_academique = ?");
                $ok = $update->execute(array($_POST['update_noms'],
                    $_POST['update_fac'],
                    $_POST['update_promotion'],
                    $_POST['update_mat'],
                    $_POST['update_annee_acad']));
                $affect = "";
                $pay = "";
                // on recupere ses affectations
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM affectation_frais WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $affect .= $d_aff['type_frais'].':'.$d_aff['montant'].', ';
                }

                // on recupere ses payements
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $pay .= $d_aff['type_frais'].':'.$d_aff['montant'].':'.$d_aff['date_payement'].':'.$d_aff['num_borderon'].', ';
                }

                // puis on efface ses informations

                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM affectation_frais WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM payement WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $titre = 'l etudiant : '.$_POST['update_mat'].' qui etait en '.$old_promotion.' '.$old_fac.' ses affectations : '.$affect.' | ses payements '.$pay; 
                // on enregistre dans la corbeille
                $d = ConnexionBdd::Connecter()->prepare("INSERT INTO corbeille(titre, date, noms_del) VALUES(?, NOW(), ?)");
                $ok = $d->execute(array($titre, VerificationUser::verif($_SESSION['data']['noms'])));
                if($ok){
                    echo 'ok';
                }else{
                    echo 'veuillez remplir tous les champs svp ...';
                }
            }else if($old_fac != $_POST['update_fac'] && $old_promotion != $_POST['update_promotion']){
                // on lui update
                $update = ConnexionBdd::Connecter()->prepare("UPDATE etudiants_inscrits SET noms = ?, fac = ?,promotion = ? WHERE matricule  = ? AND annee_academique = ?");
                $ok = $update->execute(array($_POST['update_noms'],
                    $_POST['update_fac'],
                    $_POST['update_promotion'],
                    $_POST['update_mat'],
                    $_POST['update_annee_acad']));
                $affect = "";
                $pay = "";
                // on recupere ses affectations
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM affectation_frais WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $affect .= $d_aff['type_frais'].':'.$d_aff['montant'].', ';
                }

                // on recupere ses payements
                $af = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?");
                $af->execute(array($_POST['update_mat'], $old_fac, $old_promotion, $_POST['update_annee_acad']));

                while($d_aff = $af->fetch()){
                    $pay .= $d_aff['type_frais'].':'.$d_aff['montant'].':'.$d_aff['date_payement'].':'.$d_aff['num_borderon'].', ';
                }

                // puis on efface ses informations
                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM affectation_frais WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $del_e = ConnexionBdd::Connecter()->prepare("DELETE FROM payement WHERE matricule = ? AND annee_acad = ?");
                $del_e->execute(array($_POST['update_mat'], $_POST['update_annee_acad']));

                $titre = 'l etudiant : '.$_POST['update_mat'].' qui etait en '.$old_promotion.' '.$old_fac.' ses affectations : '.$affect.' | ses payements '.$pay; 
                // on enregistre dans la corbeille
                $d = ConnexionBdd::Connecter()->prepare("INSERT INTO corbeille(titre, date, noms_del) VALUES(?, NOW(), ?)");
                $ok = $d->execute(array($titre, VerificationUser::verif($_SESSION['data']['noms'])));
                if($ok){
                    echo 'ok';
                }else{
                    echo 'la mise en corbeille n a pas reussie';
                }
            }
        }
    }else{
        echo 'veuillez remplir tous les champs svp ...';
    }
?>
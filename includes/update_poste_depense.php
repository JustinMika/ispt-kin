<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['update_pd']) && !empty($_POST['update_pd']) && $_POST['update_pd'] == "montant_update_pd"){
        if(isset($_POST['id']) && !empty($_POST['id']) && !empty($_POST['name_poste_d'])){
            if(isset($_POST['montant']) && !empty($_POST['montant'])){
                // update le montant du poste
                $update = ConnexionBdd::Connecter()->prepare("UPDATE poste_depense SET montant = ?, poste = ? WHERE id_poste = ?");
                $ok = $update->execute(array($_POST['montant'], $_POST['name_poste_d'], $_POST['id']));
                if($ok){
                    echo 'ok';
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "a mofier le montant du poste de depense :)");
                }else{
                    echo 'la mise a jour du montant n a pas reussi.';
                }
            }else{
                echo 'Veuillex renseigner le montant svp !!!';
            }
        }else{
            echo 'Veuillez vous assurer que vous avez completer tous les champs';
        }
    }else if(isset($_POST['update_trans']) && !empty($_POST['update_trans']) && $_POST['update_trans'] == "update_trans"){
        if(isset($_POST['id_trans_mod']) && !empty($_POST['id_trans_mod'])){
            if(isset($_POST['trans_post_d']) && !empty($_POST['trans_post_d'])){
                if(isset($_POST['trans_post_montant']) && !empty($_POST['trans_post_montant'])){
                    if(isset($_POST['trans_post_date_m']) && !empty($_POST['trans_post_date_m'])){
                        if(isset($_POST['trans_motif_mod']) && !empty($_POST['trans_motif_mod'])){
                            // on recuoere le dernier annee acad
                            $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
                            if($an->rowCount() > 0){
                                $an_r = $an->fetch();
                            }else{
                                $an_r['annee_acad'] = '';
                            }

                            
                            // on selectionne le poste de depense en question pour recuperer le montant
                            $sel_pd = ConnexionBdd::Connecter()->prepare("SELECT depense FROM poste_depense WHERE poste = ? AND annee_acad = ?");
                            $sel_pd->execute(array($_POST['trans_post_d'], $an_r['annee_acad']));

                            if($sel_pd->rowCount() > 0){
                                $sel_pd = $sel_pd->fetch();
                                $mont_existant = $sel_pd['depense'];
                                $mont_existant = floatval($mont_existant);

                                // if(floatval($_POST['trans_post_montant']) )
                                // update transaction
                                $update_trans = ConnexionBdd::Connecter()->prepare("UPDATE transaction_depense SET montant  = ? WHERE id = ?");
                                $ok = $update_trans->execute(array($_POST['trans_post_montant'], $_POST['id_trans_mod']));
                                if($ok){
                                    echo 'ok';
                                }else{
                                    echo 'mise a jour de la transactionn n a pas reussi ...';
                                }
                            }else{
                                echo 'le poste de depense selectionner est introuvable ...';
                            }
                        }else{

                        }
                    }else{

                    }
                }else{

                }
            }else{

            }
        }else{

        }
    }else if(isset($_POST['del_trans']) && !empty($_POST['del_trans']) && $_POST['del_trans'] == "del_trans"){
        if(isset($_POST['id_trans_mod_delete']) && !empty($_POST['id_trans_mod_delete']) && !empty($_POST['trans_id_post'])){
            if(isset($_POST['trans_post_d_delete']) && !empty($_POST['trans_post_d_delete'])){
                if(isset($_POST['trans_post_montant_delete']) && !empty($_POST['trans_post_montant_delete'])){
                    // on recuoere le dernier annee acad
                    // $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
                    // if($an->rowCount() > 0){
                    //         $an_r = $an->fetch();
                    //     }else{
                    //         $an_r['annee_acad'] = '';
                    //     }
                    // on selectionne le poste de depense en question pour recuperer le montant
                    $sel_pd = ConnexionBdd::Connecter()->prepare("SELECT depense FROM poste_depense WHERE id_poste = ?");
                    $sel_pd->execute(array($_POST['trans_id_post']));

                    if($sel_pd->rowCount() >= 1){
                        $sel_pd = $sel_pd->fetch();
                        $mont_existant = $sel_pd['depense'];
                        $mont_existant = floatval($mont_existant);

                        $n_montant  = $mont_existant - $_POST['trans_post_montant_delete'];

                        $update_trans = ConnexionBdd::Connecter()->prepare("DELETE FROM transaction_depense WHERE id_transaction = ?");
                        $ok = $update_trans->execute(array($_POST['id_trans_mod_delete']));
                        if($ok){
                            // update le montant du poste
                            $update_pd = ConnexionBdd::Connecter()->prepare("UPDATE poste_depense SET depense = ? WHERE id_poste = ?");
                            $ok = $update_pd->execute(array($n_montant, $_POST['trans_id_post']));
                            if($ok){
                                echo 'ok';
                            }else{
                                echo 'mise a jour de la transaction n\'a pas reussi ...';
                            }
                        }else{
                            echo 'Suppression de la transaction n a pas reussi.';
                        }
                    }else{
                        echo 'le poste de depense est introuvable, contacte le developpeur.';
                    }
                }else{
                    echo 'Veuillez renseigner un poste de depense.';
                }
            }else{
                echo 'Veuillez renseigner le montant.';
            }
        }else{
            echo 'Veuillez remplir tous les champs svp ...';
        }
    }else if(isset($_POST['update_trans_mod']) && !empty($_POST['update_trans_mod']) 
    && $_POST['update_trans_mod'] == "update_trans_mod"){
        if(!empty($_POST['id_trans_mod']) && !empty($_POST['trans_motif']) && !empty($_POST['num_op']) && !empty($_POST['post_trans_upp'])){
            $b = ConnexionBdd::Connecter()->prepare("UPDATE transaction_depense SET motif = ?, num_op = ? WHERE id_transaction  = ?");
            $ok = $b->execute(array($_POST['trans_motif'], $_POST['num_op'] , $_POST['id_trans_mod']));
            if($ok){
                echo 'ok';
            }else{
                die("Une erreur est survenue, lors de la mise a jours");
            }
        }else{
            die("Il y'a une erreur");
        }
    }else{
        echo 'Page de traitrement non trouver ... contacter le developpeur';
    }
?>
<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['update_pd']) && !empty($_POST['update_pd']) && $_POST['update_pd'] == "montant_update_pd"){
        if(isset($_POST['id']) && !empty($_POST['id'])){
            if(isset($_POST['montant']) && !empty($_POST['montant'])){
                // update le montant du poste
                $update = ConnexionBdd::Connecter()->prepare("UPDATE depense_facultaire SET montant = ? WHERE id_pdf = ?");
                $ok = $update->execute(array($_POST['montant'], $_POST['id']));
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
                            print_r($_POST);
                            die();
                            // on selectionne le poste de depense en question pour recuperer le montant
                            $sel_pd = ConnexionBdd::Connecter()->prepare("SELECT depense FROM poste_depense WHERE id_poste = ?");
                            $sel_pd->execute(array($_POST['trans_post_d']));

                            if($sel_pd->rowCount() > 0){
                                $sel_pd = $sel_pd->fetch();
                                $mont_existant = $sel_pd['depense'];
                                $mont_existant = floatval($mont_existant);

                                // update transaction
                                $update_trans = ConnexionBdd::Connecter()->prepare("UPDATE transaction_depense SET montant  = ? WHERE id = ?");
                                $ok = $update_trans->execute(array($_POST['trans_post_montant'], $_POST['id_trans_mod']));
                                if($ok){
                                    echo 'ok';
                                    LogUser::addlog($_SESSION['data']['noms'], "a mis a jour le montant qui du poste de depense facultaire.");
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
        if(isset($_POST['id_trans_mod_delete']) && !empty($_POST['id_trans_mod_delete'])){
            if(isset($_POST['trans_post_d_delete']) && !empty($_POST['trans_post_d_delete'])){
                if(isset($_POST['trans_post_montant_delete']) && !empty($_POST['trans_post_montant_delete']) && !empty($_POST['id_poste_del'])){
                    // print_r($_POST);
                    // die();

                    // on selectionne le poste de depense en question pour recuperer le montant
                    $sel_pd = ConnexionBdd::Connecter()->prepare("SELECT depense FROM depense_facultaire WHERE id_pdf =? ");
                    $sel_pd->execute(array($_POST['id_poste_del']));

                    if($sel_pd->rowCount() >= 1){
                        $sel_pd = $sel_pd->fetch();
                        $mont_existant = $sel_pd['depense'];
                        $mont_existant = floatval($mont_existant);

                        $n_montant  = $mont_existant - $_POST['trans_post_montant_delete'];

                        $update_trans = ConnexionBdd::Connecter()->prepare("DELETE FROM transaction_pdf WHERE id_transaction = ?");
                        $ok = $update_trans->execute(array($_POST['id_trans_mod_delete']));
                        if($ok){
                            // update le montant du poste
                            $update_pd = ConnexionBdd::Connecter()->prepare("UPDATE depense_facultaire SET depense = ? WHERE id_pdf = ?");
                            $ok = $update_pd->execute(array($n_montant, $_POST['id_poste_del']));
                            if($ok){
                                echo 'ok';
                            }else{
                                echo 'mise a jour de la transactionn n\'a pas reussi ...';
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
    }
    else{
        echo 'Page de traitrement non trouver ... contacter le developpeur';
    }
?>
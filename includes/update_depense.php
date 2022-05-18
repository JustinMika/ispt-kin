<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';
    // print_r($_POST);
    if(!empty($_POST['date_r']) && !empty($_POST['id_poste'])){
        if(!empty($_POST['motif'])){
            if(!empty($_POST['update_montant_'])){
                if(!empty($_POST['dep_post_'])){
                    if(!empty($_POST['tot_m']) && !empty($_POST['num_op'])){
                        // le dernier annee acad
                        $an =  ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                        if($an->rowCount() > 0){
                            $an_r = $an->fetch();
                        }else{
                            $an_r['id_annee'] = '';
                            die("Veuillez AJouter l annee academique");
                        }

                        // verification si la date de transaction n pas superieur a la date d'aujourdhui
                        if(date('Y-m-d',strtotime($_POST['date_r'])) <= date('Y-m-d')){
                            // update
                            $update = ConnexionBdd::Connecter()->prepare("UPDATE poste_depense SET depense = ? WHERE id_poste = ?");
                            $ok = $update->execute(array($_POST['tot_m'], $_POST['id_poste']));

                            if($ok){
                                // historique transaction
                                $hist = ConnexionBdd::Connecter()->prepare("INSERT INTO transaction_depense(montant, date_motif, motif, id_poste, id_annee, num_op) VALUES(?,?,?,?,?,?)");
                                $ok = $hist->execute(array($_POST['update_montant_'], $_POST['date_r'], $_POST['motif'], $_POST['id_poste'], $an_r['id_annee'], $_POST['num_op']));
                                if($ok){
                                    echo "ok";
                                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a fait une transaction sur le poste de dépense...');
                                }else{
                                    echo "Erreur : les donnees ne sont inserer dans la base de donnees: reessayer svp.";
                                }
                            }else{
                                echo "Erreur : les donnees ne sont inserer dans la base de donnees: reessayer svp.";
                            }
                        }else{
                            echo 'la date doit être inferieur à la date d aujourd\'hui';
                        }
                    }else{
                        echo "Veuillez renseigner le {montant}";
                    }
                }else{
                    echo "Veuillez renseigner le poste de depense";
                }
            }else{
                echo "Veuillez renseigner le montant";
            }
        }else{
            echo "Veuillez renseigner le motif";
        }
    }else{
        echo "Veuillez renseigner la date";
    }

    // function
    function formater($var){
        return htmlspecialchars($var);
    }
?>
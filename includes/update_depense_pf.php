<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // print_r($_POST);
    if(!empty($_POST['date_r'])){
        if(!empty($_POST['motif'])){
            if(!empty($_POST['update_montant_'])){
                if(!empty($_POST['dep_post_'])){
                    if(!empty($_POST['tot_montant'])){
                        if(!empty($_POST['fac_pf'])){
                            if(!empty($_POST['promotion_pf'])){
                                // verification de la date
                                if(date('Y-m-d',strtotime($_POST['date_r'])) <= date('Y-m-d')){
                                    // on recupere l'annee academique
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
                                    if($an->rowCount() > 0){
                                        $an_r = $an->fetch();
                                    }else{
                                        $an_r['annee_acad'] = '';
                                        die("Veuillez AJouter l annee academique");
                                    }
                                    
                                    $sql_update = "UPDATE depense_facultaire SET depense = ? WHERE poste = ? AND faculte = ? AND annee_acad = ?";
                                    $update = ConnexionBdd::Connecter()->prepare($sql_update);
                                    $ok = $update->execute(array($_POST['tot_montant'], $_POST['dep_post_'], $_POST['fac_pf'], $an_r['annee_acad']));

                                    if($ok){
                                        $hist = ConnexionBdd::Connecter()->prepare("INSERT INTO depense_facultaire_transact(poste_df, montant_trans, faculte, annee_acad, date_trans, motif) VALUES(?, ?, ?, ?, ?, ?)");
                                        $ok = $hist->execute(array($_POST['dep_post_'], floatval($_POST['update_montant_']), $_POST['fac_pf'], $an_r['annee_acad'], $_POST['date_r'], $_POST['motif']));

                                        if($ok){
                                            echo 'ok';
                                            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a fait une transaction sur le poste facultaire.');
                                        }else{
                                            echo 'une erreur est survenue lors de votre transaction';
                                        }
                                    }else{
                                        echo "Erreur : les donnees ne sont inserer dans la base de donnees: reessayer plus tard svp.";
                                    }
                                }else{
                                    echo 'la date doit être infierieur à la date d aujourd\'hui';
                                }
                            }else{
                                echo "Veuillez reneigner la promotion";
                            }
                        }else{
                            echo "Veuillez reneigner la faculte";
                        }
                    }else{
                        echo "Veuillez renseigner le {montant}";
                    }
                }else{
                    echo "Veuillez reneigner le poste de depense";
                }
            }else{
                echo "Veuillez reneigner le montant";
            }
        }else{
            echo "Veuillez reneigner le motif";
        }
    }else{
        echo "Veuillez reneigner la date";
    }

    // function
    function formater($var){
        return htmlentities(htmlspecialchars($var));
    }
?>
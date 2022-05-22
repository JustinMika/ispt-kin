<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // print_r($_POST);
    // die("");
    if(!empty($_POST['date_r']) && !empty($_POST['id_pdf_t'])){
        if(!empty($_POST['motif'])){
            if(!empty($_POST['update_montant_'])){
                if(!empty($_POST['dep_post_'])){
                    if(!empty($_POST['tot_montant'])){
                        if(!empty($_POST['fac_pf'])){
                            if(!empty($_POST['promotion_pf'])){
                                // verification de la date
                                if(date('Y-m-d',strtotime($_POST['date_r'])) <= date('Y-m-d')){
                                    // on recupere l'annee academique
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY id_annee ORDER BY id_annee DESC LIMIT 1");
                                    if($an->rowCount() > 0){
                                        $an_r = $an->fetch();
                                    }else{
                                        $an_r['id_annee'] = '';
                                        die("Veuillez AJouter l annee academique");
                                    }
                                    
                                    $sql_update = "UPDATE depense_facultaire SET depense = ? WHERE id_pdf = ? AND id_section = ? AND id_annee = ?";
                                    $update = ConnexionBdd::Connecter()->prepare($sql_update);
                                    $ok = $update->execute(array($_POST['tot_montant'], $_POST['id_pdf_t'], $_POST['fac_pf'], $an_r['id_annee']));

                                    if($ok){
                                        $hist = ConnexionBdd::Connecter()->prepare("INSERT INTO transaction_pdf(montant, motif, date_transaction, id_pdf, id_section, id_annee) VALUES(?, ?, ?, ?, ?, ?)");
                                        $ok = $hist->execute(array($_POST['update_montant_'], $_POST['motif'], $_POST['date_r'], $_POST['id_pdf_t'],  $_POST['fac_pf'], $an_r['id_annee']));

                                        if($ok){
                                            echo 'ok';
                                            LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'a fait une transaction sur le poste facultaire.');
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
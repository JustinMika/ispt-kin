<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['mat']) && !empty($_POST['mat'])){
        if(isset($_POST['noms']) && !empty($_POST['noms'])){
            if(isset($_POST['fac']) && !empty($_POST['fac'])){
                if(isset($_POST['promotion']) && !empty($_POST['promotion'])){
                    if(isset($_POST['annee_acad']) && !empty($_POST['annee_acad'])){
                        if(isset($_POST['pwd']) && !empty($_POST['pwd'])){
                            $pwd = htmlspecialchars(htmlentities(trim($_POST['pwd'])));
                            $pwd = sha1($pwd);

                            $mat = htmlspecialchars(htmlentities(trim($_POST['mat'])));
                            $noms = htmlspecialchars(htmlentities(trim($_POST['noms'])));
                            $faculte = htmlspecialchars(htmlentities(trim($_POST['fac'])));
                            $promotion = htmlspecialchars(htmlentities(trim($_POST['promotion'])));
                            $annee_academique = htmlspecialchars(htmlentities(trim($_POST['annee_acad'])));

                            $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND password = ? AND noms = ? AND fac = ? AND promotion = ? AND annee_academique = ?");

                            $verif->execute(array($mat, sha1($pwd), $noms, $faculte, $promotion, $annee_academique));
                            $nbre = $verif->rowcount();

                            if($nbre <= 0){
                                // verification pour le matricule, promotion, fac, annee acad
                                $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion = ? AND annee_academique = ?");
                                $v->execute(array($mat, $faculte, $promotion, $annee_academique));

                                if($v->rowCount() <= 0){
                                    // verification si le matricule existe pour l'annee donnee
                                    $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND annee_academique = ?");
                                    $v->execute(array($mat, $annee_academique));
                                    // echo($mat.', '. $pwd.', '. $noms.', '. $faculte.', '. $promotion.', '. $annee_academique);
                                    if($v->rowCount() <= 0){
                                        try {
                                            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO etudiants_inscrits(matricule, password, noms, fac, promotion, annee_academique) VALUES(?, ?, ?, ?, ?, ?)");
                                            $ok = $insert_etud->execute(array($mat, $pwd, $noms, $faculte, $promotion, $annee_academique));
                                            if($ok){
                                                // tables des etudiants
                                                $pwd = htmlspecialchars(trim($pwd));
                                                $r = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants WHERE matricule = ? AND noms = ? AND photo = '../images/etudiants.jpg' AND password = ?");
                                                $r->execute(array($mat, $noms, $pwd));
                                                if($r->rowCount() <= 0){
                                                    $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO etudiants(matricule, noms, photo, password) VALUES(?,?,'../images/etudiants.jpg', ?)");
                                                    $ok = $insert_etud->execute(array($mat, $noms, $pwd));
                                                    if($ok){
                                                        echo 'ok';
                                                    }else{
                                                        echo 'les Données ne sont pas enregistrées';
                                                    }
                                                }
                                            }else{
                                                echo "Données non inserée";
                                            }
                                        } catch (PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    }else{
                                        echo "l etudiant(e) {$mat} est est deja inscrit(e) dans l'annee acad. {$annee_academique}";
                                    }
                                }else{
                                    echo "l etudiant(e) {$mat} de {$promotion} {$faculte} est deja inscrit(e) dans l'annee acad {$annee_academique}";
                                }
                            }else{
                                echo 'Desole, ces donnees existe deja dans la base de donnees.';
                            }
                        }else{
                            echo 'le mot de passe pour l etudiant est vide';
                        }
                    }else{
                        echo 'l annee academique  de l etudiant est vide';
                    }
                }else{
                    echo 'la promotion de l etudiant est vide';
                }
            }else{
                echo 'la faculte de l etudiant est vide';
            }
        }else{
            echo 'le nomsde l etudiant est vide';
        }
    }else{
        echo 'le matricule de l etudiant est vide';
    }
?>
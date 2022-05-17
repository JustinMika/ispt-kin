<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // print_r($_POST);

    function formater($var){
        return htmlentities(htmlspecialchars(trim($var)));
    }

    if(!empty($_POST['pseudo_user'])){
        if(!empty($_POST['fonction'])){
            if(!empty($_POST['Access'])){
                if(!empty($_POST['mail_user'])){
                    if(!empty($_POST['Accpass_useress'])){
                        //verification si l utilisateur n existe pas dans la base de donnees
                        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM WHERE noms = ? AND access = ? AND email = ?");
                        $verif->execute(array(
                            formater($_POST['pseudo_user']),
                            formater($_POST['Access']),
                            formater($_POST['mail_user']),
                        ));

                        $nbre = $verif->rowCount();

                        if($nbre <= 0){
                            // insertion de l'utilisateur
                            $add_user = ConnexionBdd::Connecter()->prepare("INSERT INTO utilisateurs(noms, fonction, access, profil, email, pwd) VALUES(?,?,?,?,?,?)");
                            $ok = $add_user->execute(array(
                                formater($_POST['pseudo_user']),
                                formater($_POST['fonction']),
                                formater($_POST['Access']),
                                'img/undraw_profile.svg',
                                formater($_POST['mail_user']),
                                formater(sha1(sha1(md5($_POST['Accpass_useress']))))
                            ));

                            if($ok){
                                echo("success");
                                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 
                                strtolower('il(elle) ajouté(e)  ['.$_POST['pseudo_user'].'] parmi les les utilisateurs administrateur !.'));
                            }else{
                                die("Une erreur s'est produite. Reesayer !!!. vérifier les informations de l'utilisateur avant de l'enregistrer !");
                            }
                        }else{
                            die("l'utilisateur se trouve déjà dans la base de données");
                        }
                    }else{
                        die("le  mot de passe est vide.");
                    }
                }else{
                    die("l'adresse mail est vide.");
                }
            }else{
                die("le droit d'accèss de l'utilisateur");
            }
        }else{
            die("La fonction de l'utilisateur est vide !.");
        }
    }else{
        die("Le nom de l'utilisateur est vide !.");
    }
?>
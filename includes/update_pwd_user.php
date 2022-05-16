<?php
    session_start();
    require ('./ConnexionBdd.class.php');
    require ('./log_user.class.php');
    header('Content-type:text/html; charset=UTF-8');
    if(isset($_POST['id_user']) && !empty($_POST['id_user'])){
        if(isset($_POST['n_pwd_1']) && !empty($_POST['n_pwd_1'])){
            if(isset($_POST['n_pwd_2']) && !empty($_POST['n_pwd_2'])){
                if($_POST['n_pwd_2'] == $_POST['n_pwd_1']){
                    $pwd  = sha1(sha1(md5($_POST['n_pwd_1'])));
                    $b = ConnexionBdd::Connecter()->prepare("UPDATE utilisateurs SET pwd = ? WHERE id = ?");
                    $ok = $b->execute(array($pwd, $_POST['id_user']));
                    if($ok){
                        echo 'ok';
                        LogUser::addlog($_SESSION['data']['noms'], 'a changer son mot de passe');
                    }else{
                        echo 'Il ya une erreur.';
                    }
                }
            }else{
                echo 'le pwd 2 est vide';
            }
        }else{
            echo 'le pwd est vide.';
        }
    }else{
        echo 'Replissez tousles champs';
    }
?>
<?php
    session_start();
    require_once '../includes/ConnexionBdd.class.php';
    require_once '../includes/log_user.class.php';

    if(isset($_POST['email']) && !empty($_POST['email'])){
        if(isset($_POST['password']) && !empty($_POST['password'])){
            if(isset($_POST['user_function']) && !empty($_POST['user_function'])){
                $email = htmlentities(htmlspecialchars($_POST['email']));
                $pass = htmlentities(htmlspecialchars(sha1(sha1(md5($_POST['password'])))));
                $fonction = htmlentities(htmlspecialchars($_POST['user_function']));

                $connexion = ConnexionBdd::Connecter()->prepare("SELECT * FROM utilisateurs WHERE email = ? AND pwd = ? AND fonction = ?");
                $connexion->execute(array($email, $pass, $fonction));

                $req = $connexion->fetch();
                $nmbre = $connexion->rowcount();
                if($nmbre >= 1){
                    $_SESSION['identifiant'] = $req['id'];
                    $_SESSION['nom'] = $req['noms'];
                    $_SESSION['fonction_agent'] = $req['fonction'];
                    
                    // log de l'utilisateur
                    LogUser::addlog($req['noms'], "s'est connecté(e) Connexion au system.");
                    $data = array(
                        "id"        => md5(sha1($req['id'])), 
                        "fonction"  => $req['fonction'],
                        "noms"      => $req['noms'],
                        "profil"    => $req['profil'],
                        "id_user"   => $req['id'],
                        "access"    => $req['access']
                    );
                    
                    echo json_encode($data);
                    $_SESSION['data'] = $data;
                }else{
                    echo "<b>l'adresee email et/ou le mot de passe est incorect</b>";
                }
            }else{
                echo "<i>veuillez selectionner la fonction de l utilisateur</i>";
            }
        }
        else{
            echo "<i>le champs pour le mot de passe est peut être vide</i>";
        }
    }
    else{
        echo "<i>le champs pour l'adresse mail est peut vide.</i>";
    }
?>
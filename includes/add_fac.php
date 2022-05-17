<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // ajouter l'annee academique dans la nase de donnees
    // $faculte = $_POST['fac'];
    if(isset($_POST['fac']) && !empty($_POST['fac'])){
        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
        if($an->rowCount() > 0){
            $an_r = $an->fetch();
        }else{
            $an_r['annee_acad'] = '';
            die("Veuillez AJouter l annee academique");
        }

        $verif = ConnexionBdd::Connecter()->prepare("SELECT fac FROM faculte  WHERE fac = ? AND annee_acad = ?");
        $verif->execute(array($_POST['fac'], $an_r['annee_acad']));

        $n = $verif->rowCount();

        if($n <= 0){
            $faculte =  htmlentities(htmlspecialchars($_POST['fac']));
            $del  = ConnexionBdd::Connecter()->prepare("INSERT INTO faculte (fac, annee_acad) VALUES(?, ?)");
            $ok = $del->execute(array($_POST['fac'], $an_r['annee_acad']));
            if($ok){
                echo "ok";
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), "a ajouté une faculté");
            }else{
                echo("Erreur interne du serveur : les donnees ne sont pas enregister");
                // header("Erreur interne du serveur", true, 500);
            }
        }else{
            echo "la faculté existe deja dans la base de données pour l'annee encours ...";
        }
    }
?>
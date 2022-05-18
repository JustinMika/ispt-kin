<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // ajouter l'annee academique dans la nase de donnees
    // $faculte = $_POST['fac'];
    if(isset($_POST['fac']) && !empty($_POST['fac'])){
        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
        if($an->rowCount() > 0){
            $an_r = $an->fetch();
        }else{
            $an_r['id_annee'] = '';
            die("Veuillez AJouter l annee academique");
        }

        $verif = ConnexionBdd::Connecter()->prepare("SELECT section FROM sections  WHERE section = ? AND id_annee = ?");
        $verif->execute(array($_POST['fac'], $an_r['id_annee']));

        $n = $verif->rowCount();

        if($n <= 0){
            $faculte =  htmlentities(htmlspecialchars($_POST['fac']));
            $del  = ConnexionBdd::Connecter()->prepare("INSERT INTO sections (section, id_annee) VALUES(?, ?)");
            $ok = $del->execute(array($_POST['fac'], $an_r['id_annee']));
            if($ok){
                echo "ok";
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "a ajouté une faculté");
            }else{
                echo("Erreur interne du serveur : les donnees ne sont pas enregister");
                // header("Erreur interne du serveur", true, 500);
            }
        }else{
            echo "la faculté existe deja dans la base de données pour l'annee encours ...";
        }
    }
?>
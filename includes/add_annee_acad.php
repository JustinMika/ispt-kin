<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // ajouter l'annee academique dans la nase de donnees
    
    $annee_acad = $_POST['annee_acad'];
    if(isset($annee_acad) && !empty($annee_acad)){
        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM annee_acad WHERE annee_acad = ?");
        $verif->execute(array($annee_acad));

        $n = $verif->rowCount();

        if($n < 1){
            try {
                $annee_acad =  htmlentities(htmlspecialchars($annee_acad));
                $del  = ConnexionBdd::Connecter()->prepare("INSERT INTO annee_acad(annee_acad) VALUES(?)");
                $ok = $del->execute(array($annee_acad));
                if($ok){
                    echo "ok";
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "ajout de l'année academique.");
                }else{
                    echo("l'annee academique n'est pas enregistrée.");
                }
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }else{
            echo "l'Annee academique que vous essayer d'inserer existe deja.";
        }
    }else{
        echo("Veuillez renseigner l'annee academique a inserer dans la base de donnees.");
    }
?>
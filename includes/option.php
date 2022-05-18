<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // ajout departement
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['id_annee'] = '';
        die("Veuillez AJouter l annee academique");
    }
    // die($_POST['section_id']);
    
    $id_section = $_POST['section_id'];
    $departement = $_POST['n_depart'];
    if(isset($id_section) && isset($departement)){
        $verif = ConnexionBdd::Connecter()->prepare("SELECT departement FROM departement WHERE departement = ? AND id_section = ? AND id_annee = ?");
        $verif->execute(array($departement, $id_section, $an_r['id_annee']));
        $n = $verif->rowCount();

        if($n < 1){
            try {
                $del  = ConnexionBdd::Connecter()->prepare("INSERT INTO departement(departement, id_section, id_annee) VALUES(?, ?, ?)");
                $ok = $del->execute(array($departement, $id_section, $an_r['id_annee']));
                if($ok){
                    echo "ok";
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "ajout de d'un departement.");
                }else{
                    echo("le departement n'est pas enregistré.");
                }
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }else{
            echo "Le departement existe deja.";
        }
    }else{
        echo("Veuillez renseigner le departement a inserer dans la base de donnees.");
    }
?>
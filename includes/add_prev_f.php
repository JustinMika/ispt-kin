<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // on recupere le dernier annee academique
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    if(isset($_POST['type_Frais_pay_']) && !empty($_POST['type_Frais_pay_'])){
        $type = htmlspecialchars($_POST['type_Frais_pay_']);
        $montant = $_POST['montant_prev_p'];
        $faculte = $_POST['fac_prev_p'];
        $promotion = $_POST['promotion_prev_p'];
        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM prevision_frais WHERE type_frais = ? AND annee_acad  = ? AND faculte = ? AND promotion = ?");
        $verif->execute(array($type, $an_r['annee_acad'], $faculte, $promotion));

        $n = $verif->rowCount();

        if($n < 1){
            // insertion de donnees dans la base de donnees 
            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO prevision_frais(type_frais, annee_acad, montant, faculte, promotion) VALUES(?,?,?,?,?)");
            $ok = $insert_etud->execute(array($type, $an_r['annee_acad'], doubleval($montant),  $faculte, $promotion));
            if($ok){
                echo("ok");
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), "a ajouté '{$type}:{$montant}$' comme prevision de frais pour les etudiants de {$promotion} {$faculte} cette année");
            }else{
                echo "une erreur est survenues : les donnees ne sont pas inserer";
            }
        }else{
            echo "le type de frais : '".$_POST['type_Frais_pay_']."' existe déjà pour l'année ".$an_r['annee_acad'];
        }
    }else{
        echo "Error : Veuillez renseigner le type de frais svp ...";
    }
?>
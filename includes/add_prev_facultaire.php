<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // on recupere le dernier annee academique
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez Ajouter l annee academique");
    }

    if(isset($_POST['type_Frais_pay_']) AND !empty($_POST['type_Frais_pay_'])){
        $type = htmlspecialchars($_POST['type_Frais_pay_']);
        $montant = $_POST['montant_prev_p'];
        $faculte = $_POST['fac_prev_p'];
        $promotion = $_POST['promotion_prev_p'];
        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM 	prev_fac_frais WHERE type_frais = ? AND annee_acad  = ? AND montant = ? AND faculte = ? AND promotion = ?");
        $verif->execute(array($type, $an_r['annee_acad'], $montant,  $faculte, $promotion));

        $n = $verif->rowCount();
        // print_r($_POST);
        if($n <= 0){
            // insertion de donnees dans la base de donnees 
            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO prev_fac_frais(type_frais, faculte, promotion, montant, annee_acad) VALUES(?, ?, ?, ?, ?)");
            $ok = $insert_etud->execute(array($type, $faculte, $promotion, $montant, $an_r['annee_acad']));
            if($ok){
                echo("ok");
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), "a ajouté '{$type}:{$montant}$' comme prevision de frais facultaire pour les etudiants de {$promotion} {$faculte} cette année");
            }else{
                echo "une erreur est survenues : les donnees ne sont pas inserer";
            }
        }else{
            echo "le type de frais : '".$_POST['type_Frais_pay_']."' existe déjà pour l'année ".$annee_acad;
        }
    }else{
        echo "Error : Veuillez renseigner le type de frais svp ...";
    }
?>
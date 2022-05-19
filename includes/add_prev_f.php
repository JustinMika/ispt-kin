<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // on recupere le dernier annee academique
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['id_annee'] = '';
        die("Veuillez AJouter l annee academique");
    }

    // print_r($_POST);

    if(isset($_POST['_type_Frais_pay_']) && !empty($_POST['_type_Frais_pay_'])){
        $type = htmlspecialchars($_POST['_type_Frais_pay_']);
        $montant = $_POST['montant_prev_p'];
        $section = $_POST['section_'];
        $departement_ = $_POST['departement_'];
        $promotion = $_POST['promotion_prev_p'];
        $montant_prev_p = $_POST['montant_prev_p'];
        $option = $_POST['option_'];

        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM prevision_frais WHERE type_frais = ? AND id_annee  = ? AND promotion = ? AND id_section = ? AND id_departement = ? AND id_option = ?");
        $verif->execute(array($type, $an_r['id_annee'], $promotion, $section, $departement_, $option));

        $n = $verif->rowCount();

        if($n >= 0){
            // insertion de donnees dans la base de donnees 
            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO prevision_frais(type_frais, montant, promotion, id_section, id_departement, id_option, id_annee) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $ok = $insert_etud->execute(array($type, $montant, $promotion, $section, $departement_, $option, $an_r['id_annee']));
            if($ok){
                echo("ok");
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "a ajouté '{$type}:{$montant}$' comme prevision de frais pour les etudiants.");
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